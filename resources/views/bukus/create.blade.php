<x-layout title="Tambah Buku | SIPUS-Del">
    <div class="mx-auto max-w-3xl">
        <div class="mb-8">
            <a href="{{ route('bukus.index') }}" class="text-sm font-semibold text-forest hover:underline">&larr; Kembali ke buku</a>
            <h1 class="mt-3 text-4xl font-semibold text-charcoal">Tambah Buku</h1>
        </div>

        <form action="{{ route('bukus.store') }}" method="POST" class="space-y-6 border-y border-border-warm bg-white p-6 sm:p-8">
            @csrf
            @include('bukus.partials.form')
            <div class="flex justify-end gap-3">
                <a href="{{ route('bukus.index') }}" class="rounded-md px-4 py-2.5 text-sm font-semibold text-charcoal/65 hover:bg-sand/30">Batal</a>
                <button type="submit" class="rounded-md bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest/90">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>
