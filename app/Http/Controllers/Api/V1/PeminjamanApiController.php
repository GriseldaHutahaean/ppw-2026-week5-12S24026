<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PeminjamanResource;
use App\Models\Buku;
use App\Models\Peminjaman;
use App\Traits\ApiResponse;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PeminjamanApiController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $filters = $request->validate([
            'status' => ['sometimes', Rule::in(['dipinjam', 'dikembalikan'])],
            'nim' => ['sometimes', 'string', 'max:15'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $query = Peminjaman::query()->with('buku')->latest();

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['nim'])) {
            $query->where('nim_peminjam', $filters['nim']);
        }

        $paginator = $query->paginate(15)->withQueryString();
        $data = $paginator->getCollection()
            ->map(fn (Peminjaman $peminjaman): array => (new PeminjamanResource($peminjaman))->resolve($request))
            ->all();

        return $this->successResponse($data, 'Transaksi peminjaman berhasil dimuat.', meta: [
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

    public function show(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $peminjaman->load('buku');

        return $this->successResponse(
            (new PeminjamanResource($peminjaman))->resolve($request),
            'Detail peminjaman berhasil dimuat.',
        );
    }

    public function store(Request $request, ?Buku $buku = null): JsonResponse
    {
        $data = $request->validate([
            'buku_id' => $buku === null
                ? ['required', 'integer', 'exists:bukus,id']
                : ['prohibited'],
            'nim_peminjam' => ['required', 'string', 'min:6', 'max:15'],
            'nama_peminjam' => ['required', 'string', 'min:3', 'max:150'],
        ]);
        $bukuId = $buku?->id ?? $data['buku_id'];
        $nim = mb_strtoupper(trim($data['nim_peminjam']));

        $peminjaman = DB::transaction(function () use ($bukuId, $data, $nim): Peminjaman {
            $lockedBuku = Buku::query()->whereKey($bukuId)->lockForUpdate()->firstOrFail();

            if ($lockedBuku->stok < 1) {
                throw new HttpResponseException($this->problemResponse(
                    'Unprocessable Content',
                    422,
                    'Stok buku tidak tersedia untuk dipinjam.',
                ));
            }

            $hasActiveLoan = Peminjaman::query()
                ->where('buku_id', $lockedBuku->id)
                ->where('nim_peminjam', $nim)
                ->where('status', 'dipinjam')
                ->exists();

            if ($hasActiveLoan) {
                throw new HttpResponseException($this->problemResponse(
                    'Conflict',
                    409,
                    'Mahasiswa ini masih meminjam buku yang sama.',
                ));
            }

            $lockedBuku->decrement('stok');

            return $lockedBuku->peminjamans()->create([
                'kode_pinjam' => 'PJM-'.Str::upper((string) Str::uuid()),
                'nim_peminjam' => $nim,
                'nama_peminjam' => trim($data['nama_peminjam']),
                'tanggal_pinjam' => now()->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
                'status' => 'dipinjam',
            ])->load('buku');
        }, 3);

        return $this->createdResponse(
            (new PeminjamanResource($peminjaman))->resolve($request),
            '/api/v1/peminjamans/'.$peminjaman->id,
            'Peminjaman berhasil dicatat.',
        );
    }

    public function returnBook(Request $request, Peminjaman $peminjaman): JsonResponse
    {
        $returnedLoan = DB::transaction(function () use ($peminjaman): Peminjaman {
            $lockedPeminjaman = Peminjaman::query()
                ->whereKey($peminjaman->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($lockedPeminjaman->status === 'dikembalikan') {
                throw new HttpResponseException($this->problemResponse(
                    'Conflict',
                    409,
                    'Buku ini sudah dikembalikan sebelumnya.',
                ));
            }

            $buku = Buku::query()->whereKey($lockedPeminjaman->buku_id)->lockForUpdate()->firstOrFail();
            $lockedPeminjaman->update([
                'status' => 'dikembalikan',
                'tanggal_kembali' => now()->toDateString(),
            ]);
            $buku->increment('stok');

            return $lockedPeminjaman->load('buku');
        }, 3);

        return $this->successResponse(
            (new PeminjamanResource($returnedLoan))->resolve($request),
            'Buku berhasil dikembalikan.',
        );
    }
}
