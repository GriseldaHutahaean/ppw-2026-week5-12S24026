<x-layout title="Buku | SIPUS-Del">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-forest">Koleksi perpustakaan</p>
            <h1 class="mt-1 text-4xl font-semibold text-charcoal">Daftar Buku</h1>
            <p class="mt-2 text-sm text-charcoal/60">{{ $bukus->total() }} judul tersedia di katalog SIPUS-Del.</p>
        </div>
        <a href="{{ route('bukus.create') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-forest/90">Tambah Buku</a>
    </div>

    <div class="overflow-hidden border-y border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border-warm text-left text-sm">
                <thead class="bg-sand/25 text-xs uppercase tracking-wider text-charcoal/65">
                    <tr>
                        <th class="px-6 py-4">ISBN</th>
                        <th class="px-6 py-4">Judul</th>
                        <th class="px-6 py-4">Penulis</th>
                        <th class="px-6 py-4">Penerbit</th>
                        <th class="px-6 py-4">Tahun</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4 text-center">Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/60">
                    @forelse ($bukus as $buku)
                        <tr class="transition hover:bg-sand/20">
                            <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-charcoal/65">{{ $buku->isbn }}</td>
                            <td class="min-w-56 px-6 py-4 font-semibold text-charcoal">{{ $buku->judul }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $buku->penulis }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $buku->penerbit }}</td>
                            <td class="whitespace-nowrap px-6 py-4">{{ $buku->tahun_terbit }}</td>
                            <td class="whitespace-nowrap px-6 py-4 text-forest"><span class="border-b border-sand pb-1">{{ $buku->kategori->nama_kategori }}</span></td>
                            <td class="px-6 py-4 text-center font-semibold">{{ $buku->stok }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('bukus.show', $buku) }}" class="rounded-md px-3 py-2 text-xs font-semibold text-charcoal/65 hover:bg-sand/30">Lihat</a>
                                    <a href="{{ route('bukus.edit', $buku) }}" class="rounded-md px-3 py-2 text-xs font-semibold text-forest hover:bg-sand/30">Edit</a>
                                    <form action="{{ route('bukus.destroy', $buku) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50" onclick="return confirm('Hapus buku ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-charcoal/55">Belum ada buku.</td>
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
