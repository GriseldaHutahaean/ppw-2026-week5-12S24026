<?php

namespace Tests\Feature;

use App\Models\Buku;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeminjamanTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_librarian_can_borrow_and_return_a_book_once(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create(['stok' => 1]);

        $this->actingAs($user)
            ->post(route('peminjamans.pinjam', $buku), [
                'nim_peminjam' => '12S24001',
                'nama_peminjam' => 'Mahasiswa Del',
            ])
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 0]);

        $peminjaman = $buku->peminjamans()->firstOrFail();

        $this->post(route('peminjamans.kembali', $peminjaman))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 1]);
        $this->assertDatabaseHas('peminjamans', ['id' => $peminjaman->id, 'status' => 'dikembalikan']);

        $this->post(route('peminjamans.kembali', $peminjaman))
            ->assertSessionHas('error', 'Buku ini sudah dikembalikan sebelumnya.');
    }

    public function test_borrowing_a_book_with_no_stock_rolls_back(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create(['stok' => 0]);

        $this->actingAs($user)
            ->post(route('peminjamans.pinjam', $buku), [
                'nim_peminjam' => '12S24002',
                'nama_peminjam' => 'Mahasiswa Del',
            ])
            ->assertSessionHas('error', "Stok buku '{$buku->judul}' habis!");

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 0]);
        $this->assertDatabaseMissing('peminjamans', ['buku_id' => $buku->id]);
    }

    public function test_same_nim_cannot_borrow_the_same_book_twice(): void
    {
        $user = User::factory()->create();
        $buku = Buku::factory()->create(['stok' => 2]);
        $data = ['nim_peminjam' => '12S24003', 'nama_peminjam' => 'Mahasiswa Del'];

        $this->actingAs($user)->post(route('peminjamans.pinjam', $buku), $data);
        $this->actingAs($user)
            ->post(route('peminjamans.pinjam', $buku), $data)
            ->assertSessionHas('error', 'NIM 12S24003 masih meminjam buku ini dan belum mengembalikannya.');

        $this->assertDatabaseHas('bukus', ['id' => $buku->id, 'stok' => 1]);
        $this->assertDatabaseCount('peminjamans', 1);
    }
}
