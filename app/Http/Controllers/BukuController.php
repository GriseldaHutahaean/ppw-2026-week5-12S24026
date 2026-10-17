<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBukuRequest;
use App\Http\Requests\UpdateBukuRequest;
use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BukuController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        if ($request->filled('search')) {
            session(['last_search' => $request->string('search')->trim()->toString()]);
        }

        $bukus = Buku::with('kategori')
            ->withCount('peminjamans')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->trim()->toString();

                $query->where(function ($query) use ($search): void {
                    $query->where('judul', 'like', "%{$search}%")
                        ->orWhere('penulis', 'like', "%{$search}%")
                        ->orWhere('isbn', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('kategori_id'), fn ($query) => $query->where('kategori_id', $request->integer('kategori_id')))
            ->orderBy('judul')
            ->paginate(10)
            ->withQueryString();

        $stats = [
            'total_judul' => DB::table('bukus')->count(),
            'total_stok' => DB::table('bukus')->sum('stok'),
            'total_dipinjam' => DB::table('peminjamans')->where('status', 'dipinjam')->count(),
            'total_kembali' => DB::table('peminjamans')->where('status', 'dikembalikan')->count(),
        ];

        $kategoris = Kategori::query()->orderBy('nama_kategori')->get();

        return view('bukus.index', compact('bukus', 'stats', 'kategoris'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $kategoris = Kategori::query()->orderBy('nama_kategori')->get();

        return view('bukus.create', compact('kategoris'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBukuRequest $request): RedirectResponse
    {
        Buku::create($request->validated());

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Buku $buku): View
    {
        $buku->load('kategori');

        return view('bukus.show', compact('buku'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Buku $buku): View
    {
        $kategoris = Kategori::query()->orderBy('nama_kategori')->get();

        return view('bukus.edit', compact('buku', 'kategoris'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBukuRequest $request, Buku $buku): RedirectResponse
    {
        $buku->update($request->validated());

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Buku $buku): RedirectResponse
    {
        $buku->delete();

        return redirect()
            ->route('bukus.index')
            ->with('success', 'Buku berhasil dihapus.');
    }
}
