<x-layout title="Buku | SIPUS-Del">
    <div class="mb-9 flex flex-col gap-5 border-b border-border-warm pb-8 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Koleksi perpustakaan</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal sm:text-5xl">Daftar Buku</h1>
            <p class="mt-3 text-sm text-muted">{{ $bukus->total() }} judul sesuai pencarian di katalog SIPUS-Del.</p>
        </div>
        @auth
            <a href="{{ route('bukus.create') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-forest/90">Tambah Buku</a>
        @endauth
    </div>

    @if ($errors->any())
        <div class="mb-8 border-l-4 border-red-700 bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Kartu statistik (DB::table) --}}
    <div class="mb-8 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="border border-border-warm bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Total Judul Buku</p>
            <p class="mt-2 text-3xl font-semibold text-forest">{{ $stats['total_judul'] }}</p>
        </div>
        <div class="border border-border-warm bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Total Stok Tersedia</p>
            <p class="mt-2 text-3xl font-semibold text-forest">{{ $stats['total_stok'] }}</p>
        </div>
        <div class="border border-border-warm bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Sedang Dipinjam</p>
            <p class="mt-2 text-3xl font-semibold text-forest">{{ $stats['total_dipinjam'] }}</p>
        </div>
        <div class="border border-border-warm bg-white p-5">
            <p class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Total Dikembalikan</p>
            <p class="mt-2 text-3xl font-semibold text-forest">{{ $stats['total_kembali'] }}</p>
        </div>
    </div>

    {{-- Pencarian dinamis + filter kategori --}}
    <form method="GET" action="{{ route('bukus.index') }}" class="mb-6 flex flex-col gap-3 sm:flex-row">
        <input type="text" name="search" value="{{ request('search', session('last_search')) }}" placeholder="Cari judul, penulis, atau ISBN"
               class="w-full rounded-md border border-border-warm bg-white px-4 py-3 text-sm sm:max-w-md">
        <select name="kategori_id" class="rounded-md border border-border-warm bg-white px-4 py-3 text-sm">
            <option value="">Semua kategori</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" @selected(request('kategori_id') == $kategori->id)>{{ $kategori->nama_kategori }}</option>
            @endforeach
        </select>
        <button type="submit" class="rounded-md bg-forest px-5 py-3 text-sm font-semibold text-white hover:bg-forest/90">Cari</button>
        <a href="{{ route('bukus.index', ['search' => '']) }}" class="rounded-md border border-border-warm px-5 py-3 text-center text-sm font-semibold text-muted hover:bg-sand">Reset</a>
    </form>

    <div class="overflow-hidden border border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[1100px] divide-y divide-border-warm text-left text-sm">
                <caption class="sr-only">Daftar koleksi buku SIPUS-Del</caption>
                <thead class="bg-sand text-xs uppercase tracking-[0.12em] text-muted">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">ISBN</th>
                        <th scope="col" class="min-w-64 px-5 py-4 font-semibold">Judul</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Penulis</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Tahun</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kategori</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Stok</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Total Dipinjam</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/80">
                    @forelse ($bukus as $buku)
                        <tr class="odd:bg-sand/35 hover:bg-[#F5F3FF]">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-muted">{{ $buku->isbn }}</td>
                            <td class="min-w-64 px-5 py-4 font-semibold leading-6 text-charcoal">{{ $buku->judul }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $buku->penulis }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $buku->tahun_terbit }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-forest">{{ $buku->kategori->nama_kategori }}</td>
                            <td class="px-5 py-4 text-center font-semibold {{ $buku->stok < 1 ? 'text-red-700' : 'text-forest' }}">
                                {{ $buku->stok }}
                                @if ($buku->stok < 1)
                                    <span class="ml-1 rounded bg-red-50 px-2 py-0.5 text-xs text-red-700">Habis</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-center text-charcoal/80">{{ $buku->peminjamans_count }}</td>
                            <td class="px-5 py-4">
                                <div class="flex flex-col items-end gap-2">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('bukus.show', $buku) }}" class="rounded-md border border-border-warm px-3 py-2 text-xs font-semibold text-muted hover:border-forest hover:bg-sand hover:text-forest">Lihat</a>
                                        @auth
                                            <a href="{{ route('bukus.edit', $buku) }}" class="rounded-md border border-forest/20 px-3 py-2 text-xs font-semibold text-forest hover:border-forest hover:bg-sand">Edit</a>
                                            <form action="{{ route('bukus.destroy', $buku) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                                            </form>
                                        @endauth
                                    </div>

                                    @auth
                                        <details class="w-full max-w-xs">
                                            <summary class="cursor-pointer rounded-md bg-forest px-3 py-2 text-center text-xs font-semibold text-white hover:bg-forest/90">Pinjam</summary>
                                            <form action="{{ route('peminjamans.pinjam', $buku->id) }}" method="POST" class="mt-2 space-y-2 border border-border-warm bg-white p-3">
                                                @csrf
                                                <input type="text" name="nim_peminjam" placeholder="NIM" required minlength="6" maxlength="15"
                                                       class="w-full rounded-md border border-border-warm px-3 py-2 text-xs">
                                                <input type="text" name="nama_peminjam" placeholder="Nama peminjam" required minlength="3" maxlength="150"
                                                       value="{{ session('last_peminjam') }}"
                                                       class="w-full rounded-md border border-border-warm px-3 py-2 text-xs">
                                                <button type="submit" class="w-full rounded-md border border-forest px-3 py-2 text-xs font-semibold text-forest hover:bg-sand">Proses Peminjaman</button>
                                            </form>
                                        </details>
                                    @endauth
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-14 text-center text-muted">Belum ada buku.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($bukus->hasPages())
        <div class="mt-6">{{ $bukus->links() }}</div>
    @endif
</x-layout>
