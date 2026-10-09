<x-layout title="Detail Buku | SIPUS-Del">
    <div class="mx-auto max-w-4xl">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('bukus.index') }}" class="text-sm font-semibold text-forest hover:underline">&larr; Kembali ke buku</a>
                <h1 class="mt-3 text-4xl font-semibold text-charcoal">Detail Buku</h1>
            </div>
            <a href="{{ route('bukus.edit', $buku) }}" class="rounded-md bg-forest px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-forest/90">Edit Buku</a>
        </div>

        <div class="border-y border-border-warm bg-white p-6 sm:p-8">
            <h2 class="text-3xl font-semibold text-charcoal">{{ $buku->judul }}</h2>
            <p class="mt-2 text-sm text-forest">{{ $buku->kategori->nama_kategori }}</p>
            <dl class="mt-8 grid gap-6 sm:grid-cols-2">
                <div><dt class="text-sm font-semibold text-charcoal/55">ISBN</dt><dd class="mt-1 font-mono text-sm">{{ $buku->isbn }}</dd></div>
                <div><dt class="text-sm font-semibold text-charcoal/55">Penulis</dt><dd class="mt-1 text-sm">{{ $buku->penulis }}</dd></div>
                <div><dt class="text-sm font-semibold text-charcoal/55">Penerbit</dt><dd class="mt-1 text-sm">{{ $buku->penerbit }}</dd></div>
                <div><dt class="text-sm font-semibold text-charcoal/55">Tahun Terbit</dt><dd class="mt-1 text-sm">{{ $buku->tahun_terbit }}</dd></div>
                <div><dt class="text-sm font-semibold text-charcoal/55">Stok</dt><dd class="mt-1 text-sm">{{ $buku->stok }}</dd></div>
            </dl>
            <div class="mt-8 border-t border-border-warm pt-6">
                <h3 class="text-sm font-semibold text-charcoal/55">Sinopsis</h3>
                <p class="mt-2 whitespace-pre-line text-sm leading-7 text-charcoal/75">{{ $buku->sinopsis ?: 'Belum ada sinopsis.' }}</p>
            </div>
        </div>
    </div>
</x-layout>
