<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Peminjaman;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with('buku')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('peminjamans.index', compact('peminjamans'));
    }

    public function pinjam(Request $request, int $id): RedirectResponse
    {
        $validated = $request->validate([
            'nim_peminjam' => 'required|string|min:6|max:15',
            'nama_peminjam' => 'required|string|min:3|max:150',
        ]);

        try {
            DB::transaction(function () use ($validated, $id) {
                // Kunci baris buku sampai transaksi selesai
                $buku = Buku::where('id', $id)->lockForUpdate()->firstOrFail();

                // Tolak dan rollback jika stok habis
                if ($buku->stok < 1) {
                    throw new \RuntimeException("Stok buku '{$buku->judul}' habis!");
                }

                $nim = strtoupper(trim($validated['nim_peminjam']));

                // Cegah NIM yang sama meminjam buku yang sama sebelum dikembalikan
                $masihPinjam = Peminjaman::where('buku_id', $id)
                    ->where('nim_peminjam', $nim)
                    ->where('status', 'dipinjam')
                    ->exists();

                if ($masihPinjam) {
                    throw new \RuntimeException("NIM {$nim} masih meminjam buku ini dan belum mengembalikannya.");
                }

                $buku->decrement('stok');

                $buku->peminjamans()->create([
                    'kode_pinjam' => 'PJM-'.strtoupper(Str::random(8)),
                    'nim_peminjam' => $nim,
                    'nama_peminjam' => $validated['nama_peminjam'],
                    'tanggal_pinjam' => now()->toDateString(),
                    'tanggal_jatuh_tempo' => now()->addDays(7)->toDateString(),
                    'status' => 'dipinjam',
                ]);
            }, 3);

            // State sesi: peminjam terakhir
            session(['last_peminjam' => $validated['nama_peminjam']]);

            return back()->with('success', 'Peminjaman berhasil dicatat.');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION][pinjam]: '.$e->getMessage());

            return back()->withInput()->with('error', 'Terjadi kesalahan database saat meminjam buku.');
        }
    }

    public function kembali(int $id): RedirectResponse
    {
        try {
            DB::transaction(function () use ($id) {
                $peminjaman = Peminjaman::where('id', $id)->lockForUpdate()->firstOrFail();

                if ($peminjaman->status === 'dikembalikan') {
                    throw new \RuntimeException('Buku ini sudah dikembalikan sebelumnya.');
                }

                $buku = Buku::where('id', $peminjaman->buku_id)->lockForUpdate()->firstOrFail();

                $peminjaman->update([
                    'status' => 'dikembalikan',
                    'tanggal_kembali' => now()->toDateString(),
                ]);

                $buku->increment('stok');
            }, 3);

            return back()->with('success', 'Buku berhasil dikembalikan.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        } catch (QueryException $e) {
            Log::error('[DB EXCEPTION][kembali]: '.$e->getMessage());

            return back()->with('error', 'Terjadi kesalahan database saat mengembalikan buku.');
        }
    }
}
