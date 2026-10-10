<x-layout title="Edit Buku | SIPUS-Del">
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="{{ route('bukus.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-forest hover:text-forest/70">&larr; Kembali ke buku</a>
            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-brass">Koleksi perpustakaan</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal">Edit Buku</h1>
            <p class="mt-3 text-sm text-muted">Perbarui informasi buku tanpa mengubah struktur data katalog.</p>
        </div>

        <form action="{{ route('bukus.update', $buku) }}" method="POST" class="space-y-7 border border-border-warm bg-white p-6 sm:p-8">
            @csrf
            @method('PUT')
            @include('bukus.partials.form')
            <div class="flex flex-col-reverse justify-end gap-3 border-t border-border-warm pt-6 sm:flex-row">
                <a href="{{ route('bukus.index') }}" class="rounded-md border border-border-warm px-4 py-3 text-center text-sm font-semibold text-muted hover:bg-sand hover:text-charcoal">Batal</a>
                <button type="submit" class="rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white hover:bg-forest/90">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layout>
