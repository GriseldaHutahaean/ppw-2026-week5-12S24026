<x-layout title="Detail Kategori | SIPUS-Del">
    <div class="mx-auto max-w-2xl">
        <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <a href="{{ route('kategoris.index') }}" class="text-sm font-semibold text-forest hover:underline">&larr; Kembali ke kategori</a>
                <h1 class="mt-3 text-4xl font-semibold text-charcoal">Detail Kategori</h1>
            </div>
            <a href="{{ route('kategoris.edit', $kategori) }}" class="rounded-md bg-forest px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-forest/90">Edit Kategori</a>
        </div>

        <dl class="divide-y divide-border-warm bg-white">
            <div class="grid gap-1 px-6 py-5 sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-semibold text-charcoal/55">Kode Kategori</dt>
                <dd class="text-sm font-bold text-forest sm:col-span-2">{{ $kategori->kode_kategori }}</dd>
            </div>
            <div class="grid gap-1 px-6 py-5 sm:grid-cols-3 sm:gap-4">
                <dt class="text-sm font-semibold text-charcoal/55">Nama Kategori</dt>
                <dd class="text-sm text-charcoal sm:col-span-2">{{ $kategori->nama_kategori }}</dd>
            </div>
        </dl>
    </div>
</x-layout>
