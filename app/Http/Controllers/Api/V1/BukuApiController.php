<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\BukuResource;
use App\Models\Buku;
use App\Traits\ApiResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class BukuApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $sortOptions = ['judul', '-judul', 'tahun_terbit', '-tahun_terbit', 'stok', '-stok', 'created_at', '-created_at'];
        $filters = $request->validate([
            'search' => ['sometimes', 'string', 'max:200'],
            'kategori_id' => ['sometimes', 'integer', 'exists:kategoris,id'],
            'tersedia' => ['sometimes', Rule::in(['0', '1', 0, 1])],
            'sort' => ['sometimes', Rule::in($sortOptions)],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = Buku::query()
            ->with('kategori')
            ->withCount('peminjamans');

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $builder) use ($search): void {
                $builder->where('judul', 'like', '%'.$search.'%')
                    ->orWhere('penulis', 'like', '%'.$search.'%')
                    ->orWhere('isbn', 'like', '%'.$search.'%');
            });
        }

        if (isset($filters['kategori_id'])) {
            $query->where('kategori_id', $filters['kategori_id']);
        }

        if (isset($filters['tersedia']) && (string) $filters['tersedia'] === '1') {
            $query->where('stok', '>', 0);
        }

        if (isset($filters['tersedia']) && (string) $filters['tersedia'] === '0') {
            $query->where('stok', 0);
        }

        $sort = $filters['sort'] ?? '-created_at';
        $direction = str_starts_with($sort, '-') ? 'desc' : 'asc';
        $column = ltrim($sort, '-');
        $paginator = $query->orderBy($column, $direction)->paginate(15)->withQueryString();
        $data = $paginator->getCollection()
            ->map(fn (Buku $buku): array => (new BukuResource($buku))->resolve($request))
            ->all();

        return $this->successResponse($data, 'Katalog buku berhasil dimuat.', meta: [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
        ], links: [
            'first' => $paginator->url(1),
            'last' => $paginator->url($paginator->lastPage()),
            'prev' => $paginator->previousPageUrl(),
            'next' => $paginator->nextPageUrl(),
        ]);
    }

    public function show(Request $request, Buku $buku): JsonResponse
    {
        $buku->load(['kategori', 'peminjamans' => fn ($query) => $query->latest('tanggal_pinjam')]);

        return $this->successResponse(
            (new BukuResource($buku))->resolve($request),
            'Detail buku berhasil dimuat.',
        );
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->bookRules());
        $buku = Buku::create($data)->load('kategori')->loadCount('peminjamans');

        return $this->createdResponse(
            (new BukuResource($buku))->resolve($request),
            '/api/v1/bukus/'.$buku->id,
            'Buku berhasil dibuat.',
        );
    }

    public function replace(Request $request, Buku $buku): JsonResponse
    {
        $data = $request->validate($this->bookRules($buku, fullReplacement: true));
        $buku->update($data);
        $buku->load('kategori')->loadCount('peminjamans');

        return $this->successResponse(
            (new BukuResource($buku))->resolve($request),
            'Buku berhasil diganti.',
        );
    }

    public function update(Request $request, Buku $buku): JsonResponse
    {
        $data = $request->validate($this->bookRules($buku, partial: true));
        $buku->update($data);
        $buku->load('kategori')->loadCount('peminjamans');

        return $this->successResponse(
            (new BukuResource($buku))->resolve($request),
            'Buku berhasil diperbarui.',
        );
    }

    public function destroy(Buku $buku): JsonResponse
    {
        $buku->getConnection()->transaction(function () use ($buku): void {
            $lockedBuku = Buku::query()->whereKey($buku->getKey())->lockForUpdate()->firstOrFail();

            if ($lockedBuku->peminjamans()->where('status', 'dipinjam')->exists()) {
                throw new HttpException(409, 'Buku masih memiliki peminjaman aktif.');
            }

            $lockedBuku->delete();
        }, 3);

        return $this->noContentResponse();
    }

    public function externalReference(Buku $buku): JsonResponse
    {
        try {
            $response = Http::timeout(8)->get('https://openlibrary.org/api/books', [
                'bibkeys' => 'ISBN:'.$buku->isbn,
                'format' => 'json',
                'jscmd' => 'data',
            ]);
        } catch (ConnectionException) {
            return $this->successResponse([
                'found' => false,
                'source' => 'OpenLibrary',
                'data' => null,
                'message' => 'Referensi eksternal tidak dapat dihubungi.',
            ], 'Referensi eksternal tidak tersedia.');
        }

        if ($response->notFound()) {
            return $this->externalReferenceFallback();
        }

        if (! $response->successful()) {
            return $this->problemResponse(
                'Bad Gateway',
                502,
                'Layanan referensi eksternal mengembalikan respons yang tidak dapat digunakan.',
            );
        }

        $payload = $response->json();
        $reference = is_array($payload) ? ($payload['ISBN:'.$buku->isbn] ?? null) : null;

        if (! is_array($reference)) {
            return $this->externalReferenceFallback();
        }

        return $this->successResponse([
            'found' => true,
            'source' => 'OpenLibrary',
            'data' => $reference,
        ], 'Referensi eksternal ditemukan.');
    }

    /**
     * @return array<string, mixed>
     */
    private function bookRules(?Buku $buku = null, bool $fullReplacement = false, bool $partial = false): array
    {
        $required = $partial ? 'sometimes' : 'required';
        $uniqueIsbn = Rule::unique('bukus', 'isbn');

        if ($buku !== null) {
            $uniqueIsbn->ignore($buku->id);
        }

        return [
            'isbn' => [$required, 'string', 'max:20', $uniqueIsbn],
            'judul' => [$required, 'string', 'min:5', 'max:255'],
            'penulis' => [$required, 'string', 'max:255'],
            'penerbit' => [$required, 'string', 'max:255'],
            'tahun_terbit' => [$required, 'integer', 'between:1000,'.now()->year],
            'kategori_id' => [$required, 'integer', 'exists:kategoris,id'],
            'stok' => [$required, 'integer', 'min:0'],
            'sinopsis' => [$partial ? 'sometimes' : ($fullReplacement ? 'required' : 'nullable'), 'nullable', 'string'],
        ];
    }

    private function externalReferenceFallback(): JsonResponse
    {
        return $this->successResponse([
            'found' => false,
            'source' => 'OpenLibrary',
            'data' => null,
            'message' => 'Referensi untuk ISBN ini tidak ditemukan.',
        ], 'Referensi eksternal tidak ditemukan.');
    }
}
