<?php

namespace Tests\Feature\Api\V1;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BukuApiControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_catalog_filters_results_and_only_offers_borrowing_when_stock_is_available(): void
    {
        $kategori = Kategori::factory()->create();
        $availableBook = Buku::factory()->for($kategori)->create(['judul' => 'Belajar Laravel', 'stok' => 2]);
        Buku::factory()->for($kategori)->create(['judul' => 'Belajar SQL', 'stok' => 0]);

        $this->getJson('/api/v1/bukus?search=Laravel&kategori_id='.$kategori->id.'&tersedia=1&sort=judul')
            ->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.id', $availableBook->id)
            ->assertJsonPath('data.0._links.pinjam_buku.href', '/api/v1/bukus/'.$availableBook->id.'/peminjamans');
    }

    public function test_book_detail_returns_404_problem_details_when_the_book_does_not_exist(): void
    {
        $this->getJson('/api/v1/bukus/999999')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('title', 'Not Found')
            ->assertJsonPath('status', 404);
    }

    public function test_unknown_api_route_returns_a_problem_details_not_found_response(): void
    {
        $this->getJson('/api/v1/not-a-route')
            ->assertNotFound()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('status', 404);
    }

    public function test_unavailable_book_does_not_expose_a_borrow_link(): void
    {
        $buku = Buku::factory()->create(['stok' => 0]);

        $this->getJson('/api/v1/bukus/'.$buku->id)
            ->assertOk()
            ->assertJsonMissingPath('data._links.pinjam_buku');
    }

    public function test_book_creation_requires_a_bearer_token_and_returns_a_location_header(): void
    {
        $kategori = Kategori::factory()->create();
        $payload = $this->bookPayload($kategori);

        $this->postJson('/api/v1/bukus', $payload)
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');

        $user = User::factory()->create();
        $response = $this->withToken($user->createToken('test')->plainTextToken)
            ->postJson('/api/v1/bukus', $payload)
            ->assertCreated();

        $this->assertSame('/api/v1/bukus/'.$response->json('data.id'), $response->headers->get('Location'));
        $this->assertDatabaseHas('bukus', ['isbn' => $payload['isbn'], 'stok' => 3]);
    }

    public function test_put_requires_full_replacement_while_patch_accepts_a_partial_update(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create(['stok' => 3]);
        $token = $user->createToken('test')->plainTextToken;

        $this->withToken($token)
            ->putJson('/api/v1/bukus/'.$buku->id, ['stok' => 8])
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('status', 422);

        $this->withToken($token)
            ->patchJson('/api/v1/bukus/'.$buku->id, ['stok' => 8])
            ->assertOk()
            ->assertJsonPath('data.stok', 8);

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 8]);
    }

    public function test_book_creation_rejects_duplicate_isbn_invalid_category_and_negative_stock(): void
    {
        $user = User::factory()->create();
        $existing = Buku::factory()->create(['isbn' => '9786021234567']);
        $payload = $this->bookPayload($existing->kategori);
        $payload['stok'] = -1;
        $payload['kategori_id'] = 999999;

        $this->withToken($user->createToken('test')->plainTextToken)
            ->postJson('/api/v1/bukus', $payload)
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/problem+json')
            ->assertJsonPath('status', 422)
            ->assertJsonStructure(['errors' => ['isbn', 'kategori_id', 'stok']]);

        $this->assertDatabaseCount('bukus', 1);
    }

    public function test_active_loan_blocks_book_deletion_with_a_problem_details_conflict(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create();
        $this->createLoan($buku);

        $this->withToken($user->createToken('test')->plainTextToken)
            ->deleteJson('/api/v1/bukus/'.$buku->id)
            ->assertConflict()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->assertModelExists($buku);
    }

    public function test_book_without_active_loans_is_deleted_with_no_content(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create();

        $this->withToken($user->createToken('test')->plainTextToken)
            ->deleteJson('/api/v1/bukus/'.$buku->id)
            ->assertNoContent();

        $this->assertModelMissing($buku);
    }

    public function test_borrowing_and_returning_update_stock_and_hateoas_links(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create(['stok' => 1]);
        $token = $user->createToken('test')->plainTextToken;
        $payload = ['nim_peminjam' => '12S24010', 'nama_peminjam' => 'Mahasiswa Del'];

        $created = $this->withToken($token)
            ->postJson('/api/v1/bukus/'.$buku->id.'/peminjamans', $payload)
            ->assertCreated()
            ->assertJsonPath('data.status', 'dipinjam')
            ->assertJsonPath('data._links.kembalikan_buku.method', 'PATCH');

        $peminjamanId = $created->json('data.id');
        $this->assertSame('/api/v1/peminjamans/'.$peminjamanId, $created->headers->get('Location'));
        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 0]);

        $this->withToken($token)
            ->patchJson('/api/v1/peminjamans/'.$peminjamanId.'/kembalikan')
            ->assertOk()
            ->assertJsonPath('data.status', 'dikembalikan')
            ->assertJsonMissingPath('data._links.kembalikan_buku');

        $this->withToken($token)
            ->patchJson('/api/v1/peminjamans/'.$peminjamanId.'/kembalikan')
            ->assertConflict()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 1]);
        $this->assertDatabaseHas('peminjamans', [
            'id' => $peminjamanId,
            'status' => 'dikembalikan',
            'tanggal_kembali' => now()->toDateString().' 00:00:00',
        ]);
    }

    public function test_unavailable_stock_and_duplicate_active_loan_are_rejected_without_state_changes(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;
        $outOfStock = Buku::factory()->create(['stok' => 0]);

        $this->withToken($token)
            ->postJson('/api/v1/peminjamans', [
                'buku_id' => $outOfStock->id,
                'nim_peminjam' => '12S24011',
                'nama_peminjam' => 'Mahasiswa Del',
            ])
            ->assertUnprocessable()
            ->assertHeader('Content-Type', 'application/problem+json');

        $available = Buku::factory()->create(['stok' => 2]);
        $this->createLoan($available, ['nim_peminjam' => '12S24012']);

        $this->withToken($token)
            ->postJson('/api/v1/peminjamans', [
                'buku_id' => $available->id,
                'nim_peminjam' => '12S24012',
                'nama_peminjam' => 'Mahasiswa Del',
            ])
            ->assertConflict()
            ->assertHeader('Content-Type', 'application/problem+json');

        $this->assertDatabaseHas('bukus', ['id' => $outOfStock->id, 'stok' => 0]);
        $this->assertDatabaseHas('bukus', ['id' => $available->id, 'stok' => 2]);
        $this->assertDatabaseCount('peminjamans', 1);
    }

    public function test_login_issues_a_bearer_token_and_logout_revokes_it(): void
    {
        $user = User::factory()->create(['email' => 'librarian@example.test', 'password' => 'password123']);

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => 'librarian@example.test',
            'password' => 'password123',
        ])->assertOk()->assertJsonPath('data.token_type', 'Bearer');
        $token = $login->json('data.access_token');

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertOk()
            ->assertJsonPath('data.id', $user->id);

        $this->withToken($token)->postJson('/api/v1/auth/logout')->assertOk();

        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $user->id]);
        Auth::forgetGuards();

        $this->withToken($token)->getJson('/api/v1/auth/me')
            ->assertUnauthorized()
            ->assertHeader('Content-Type', 'application/problem+json');
    }

    public function test_external_reference_returns_a_safe_fallback_when_openlibrary_is_unavailable(): void
    {
        Http::preventStrayRequests();
        Http::fake([
            'openlibrary.org/api/books*' => Http::failedConnection(),
        ]);
        $buku = Buku::factory()->create();

        $this->getJson('/api/v1/bukus/'.$buku->id.'/referensi-eksternal')
            ->assertOk()
            ->assertJsonPath('data.found', false)
            ->assertJsonPath('data.source', 'OpenLibrary')
            ->assertJsonPath('data.data', null);

        Http::assertSent(fn ($request): bool => str_contains($request->url(), 'ISBN%3A'.$buku->isbn));
    }

    /**
     * @return array<string, mixed>
     */
    private function bookPayload(Kategori $kategori): array
    {
        return [
            'isbn' => '9786021234567',
            'judul' => 'Pemrograman REST API',
            'penulis' => 'Pustakawan Del',
            'penerbit' => 'Penerbit Del',
            'tahun_terbit' => 2024,
            'kategori_id' => $kategori->id,
            'stok' => 3,
        ];
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function createLoan(Buku $buku, array $attributes = []): Peminjaman
    {
        return $buku->peminjamans()->create(array_merge([
            'kode_pinjam' => 'PJM-TEST-'.strtoupper(substr(hash('sha256', $attributes['nim_peminjam'] ?? '12S24099'), 0, 12)),
            'nim_peminjam' => '12S24099',
            'nama_peminjam' => 'Mahasiswa Del',
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
            'status' => 'dipinjam',
        ], $attributes));
    }
}
