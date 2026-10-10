<x-layout title="Buku | SIPUS-Del">
    <div class="mb-9 flex flex-col gap-5 border-b border-border-warm pb-8 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Koleksi perpustakaan</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal sm:text-5xl">Daftar Buku</h1>
            <p class="mt-3 text-sm text-muted">{{ $bukus->total() }} judul tersedia di katalog SIPUS-Del.</p>
        </div>
        <a href="{{ route('bukus.create') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-forest/90">Tambah Buku</a>
    </div>

    <div class="overflow-hidden border border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] divide-y divide-border-warm text-left text-sm">
                <caption class="sr-only">Daftar koleksi buku SIPUS-Del</caption>
                <thead class="bg-sand text-xs uppercase tracking-[0.12em] text-muted">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">ISBN</th>
                        <th scope="col" class="min-w-64 px-5 py-4 font-semibold">Judul</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Penulis</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Penerbit</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Tahun</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Kategori</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Stok</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/80">
                    @forelse ($bukus as $buku)
                        <tr class="odd:bg-sand/35 hover:bg-[#E7EFEA]">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-muted">{{ $buku->isbn }}</td>
                            <td class="min-w-64 px-5 py-4 font-semibold leading-6 text-charcoal">{{ $buku->judul }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $buku->penulis }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $buku->penerbit }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $buku->tahun_terbit }}</td>
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-forest">{{ $buku->kategori->nama_kategori }}</td>
                            <td class="px-5 py-4 text-center font-semibold text-forest">{{ $buku->stok }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('bukus.show', $buku) }}" class="rounded-md border border-border-warm px-3 py-2 text-xs font-semibold text-muted hover:border-forest hover:bg-sand hover:text-forest">Lihat</a>
                                    <a href="{{ route('bukus.edit', $buku) }}" class="rounded-md border border-forest/20 px-3 py-2 text-xs font-semibold text-forest hover:border-forest hover:bg-sand">Edit</a>
                                    <form action="{{ route('bukus.destroy', $buku) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                                    </form>
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
