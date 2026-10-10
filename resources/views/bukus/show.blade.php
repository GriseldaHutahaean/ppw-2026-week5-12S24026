<x-layout title="Detail Buku | SIPUS-Del">
    <div class="mx-auto max-w-4xl">
        <div class="mb-8 flex flex-col gap-5 border-b border-border-warm pb-8 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('bukus.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-forest hover:text-forest/70">&larr; Kembali ke buku</a>
                <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-brass">Koleksi perpustakaan</p>
                <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal">Detail Buku</h1>
            </div>
            <a href="{{ route('bukus.edit', $buku) }}" class="rounded-md bg-forest px-4 py-3 text-center text-sm font-semibold text-white hover:bg-forest/90">Edit Buku</a>
        </div>

        <div class="border border-border-warm bg-white p-6 sm:p-9">
            <h2 class="text-3xl font-semibold leading-tight text-charcoal">{{ $buku->judul }}</h2>
            <p class="mt-3 text-sm font-semibold text-forest">{{ $buku->kategori->nama_kategori }}</p>
            <dl class="mt-9 grid gap-x-8 gap-y-7 border-y border-border-warm py-7 sm:grid-cols-2">
                <div><dt class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">ISBN</dt><dd class="mt-2 font-mono text-sm text-charcoal">{{ $buku->isbn }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Penulis</dt><dd class="mt-2 text-sm text-charcoal">{{ $buku->penulis }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Penerbit</dt><dd class="mt-2 text-sm text-charcoal">{{ $buku->penerbit }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Tahun Terbit</dt><dd class="mt-2 text-sm text-charcoal">{{ $buku->tahun_terbit }}</dd></div>
                <div><dt class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Stok</dt><dd class="mt-2 text-sm font-semibold text-forest">{{ $buku->stok }}</dd></div>
            </dl>
            <div class="pt-1">
                <h3 class="text-xs font-semibold uppercase tracking-[0.12em] text-muted">Sinopsis</h3>
                <p class="mt-3 whitespace-pre-line text-sm leading-7 text-charcoal/75">{{ $buku->sinopsis ?: 'Belum ada sinopsis.' }}</p>
            </div>
        </div>
    </div>
</x-layout>
