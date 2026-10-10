<x-layout title="Edit Kategori | SIPUS-Del">
    <div class="mx-auto max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('kategoris.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-forest hover:text-forest/70">&larr; Kembali ke kategori</a>
            <p class="mt-6 text-xs font-semibold uppercase tracking-[0.2em] text-brass">Master data</p>
            <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal">Edit Kategori</h1>
            <p class="mt-3 text-sm text-muted">Perbarui nama atau kode kategori yang dipilih.</p>
        </div>

        <form action="{{ route('kategoris.update', $kategori) }}" method="POST" class="space-y-7 border border-border-warm bg-white p-6 sm:p-8">
            @csrf
            @method('PUT')
            <div>
                <label for="kode_kategori" class="mb-2 block text-sm font-semibold text-charcoal">Kode Kategori</label>
                <input id="kode_kategori" name="kode_kategori" type="text" value="{{ old('kode_kategori', $kategori->kode_kategori) }}" class="w-full rounded-md bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
                @error('kode_kategori')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nama_kategori" class="mb-2 block text-sm font-semibold text-charcoal">Nama Kategori</label>
                <input id="nama_kategori" name="nama_kategori" type="text" value="{{ old('nama_kategori', $kategori->nama_kategori) }}" class="w-full rounded-md bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
                @error('nama_kategori')<p class="mt-2 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="flex flex-col-reverse justify-end gap-3 border-t border-border-warm pt-6 sm:flex-row">
                <a href="{{ route('kategoris.index') }}" class="rounded-md border border-border-warm px-4 py-3 text-center text-sm font-semibold text-muted hover:bg-sand hover:text-charcoal">Batal</a>
                <button type="submit" class="rounded-md bg-forest px-4 py-3 text-sm font-semibold text-white hover:bg-forest/90">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</x-layout>
