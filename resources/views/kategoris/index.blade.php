<x-layout title="Kategori | SIPUS-Del">
    <div class="mb-9 flex flex-col gap-5 border-b border-border-warm pb-8 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Master data</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal sm:text-5xl">Daftar Kategori</h1>
            <p class="mt-3 text-sm text-muted">{{ $kategoris->total() }} kategori tersimpan dalam katalog.</p>
        </div>
        <a href="{{ route('kategoris.create') }}" class="inline-flex items-center justify-center rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white shadow-sm hover:bg-forest/90">Tambah Kategori</a>
    </div>

    <div class="overflow-hidden border border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-border-warm text-left text-sm">
                <caption class="sr-only">Daftar kategori buku SIPUS-Del</caption>
                <thead class="bg-sand text-xs uppercase tracking-[0.12em] text-muted">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">Kode</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Nama Kategori</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/80">
                    @forelse ($kategoris as $kategori)
                        <tr class="odd:bg-sand/35 hover:bg-[#E7EFEA]">
                            <td class="whitespace-nowrap px-5 py-4 font-semibold text-forest">{{ $kategori->kode_kategori }}</td>
                            <td class="px-5 py-4 text-charcoal/85">{{ $kategori->nama_kategori }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('kategoris.show', $kategori) }}" class="rounded-md border border-border-warm px-3 py-2 text-xs font-semibold text-muted hover:border-forest hover:bg-sand hover:text-forest">Lihat</a>
                                    <a href="{{ route('kategoris.edit', $kategori) }}" class="rounded-md border border-forest/20 px-3 py-2 text-xs font-semibold text-forest hover:border-forest hover:bg-sand">Edit</a>
                                    <form action="{{ route('kategoris.destroy', $kategori) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-md border border-red-200 px-3 py-2 text-xs font-semibold text-red-700 hover:bg-red-50" onclick="return confirm('Hapus kategori ini?')">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-14 text-center text-muted">Belum ada kategori.</td>
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
