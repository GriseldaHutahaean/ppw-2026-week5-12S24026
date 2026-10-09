<x-layout title="Kategori | SIPUS-Del">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-forest">Master data</p>
            <h1 class="mt-1 text-4xl font-semibold text-charcoal">Daftar Kategori</h1>
            <p class="mt-2 text-sm text-charcoal/60">{{ $kategoris->total() }} kategori tersimpan dalam katalog.</p>
        </div>
        <a href="{{ route('kategoris.create') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-forest/90">Tambah Kategori</a>
    </div>

    <div class="overflow-hidden border-y border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border-warm text-left text-sm">
                <thead class="bg-sand/25 text-xs uppercase tracking-wider text-charcoal/65">
                    <tr>
                        <th class="px-6 py-4">Kode</th>
                        <th class="px-6 py-4">Nama Kategori</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/60">
                    @forelse ($kategoris as $kategori)
                        <tr class="transition hover:bg-sand/20">
                            <td class="whitespace-nowrap px-6 py-4 font-semibold text-forest">{{ $kategori->kode_kategori }}</td>
                            <td class="px-6 py-4">{{ $kategori->nama_kategori }}</td>
                            <td class="px-6 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('kategoris.show', $kategori) }}" class="rounded-md px-3 py-2 text-xs font-semibold text-charcoal/65 hover:bg-sand/30">Lihat</a>
                                    <a href="{{ route('kategoris.edit', $kategori) }}" class="rounded-md px-3 py-2 text-xs font-semibold text-forest hover:bg-sand/30">Edit</a>
                                    <form action="{{ route('kategoris.destroy', $kategori) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md px-3 py-2 text-xs font-semibold text-red-600 hover:bg-red-50" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-charcoal/55">Belum ada kategori.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($kategoris->hasPages())
        <div class="mt-6">{{ $kategoris->links() }}</div>
    @endif
</x-layout>
