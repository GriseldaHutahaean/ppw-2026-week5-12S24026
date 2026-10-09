<x-layout title="Tambah Kategori | SIPUS-Del">
    <div class="mx-auto max-w-2xl">
        <div class="mb-8">
            <a href="{{ route('kategoris.index') }}" class="text-sm font-semibold text-forest hover:underline">&larr; Kembali ke kategori</a>
            <h1 class="mt-3 text-4xl font-semibold text-charcoal">Tambah Kategori</h1>
        </div>

        <form action="{{ route('kategoris.store') }}" method="POST" class="space-y-6 border-y border-border-warm bg-white p-6 sm:p-8">
            @csrf
            <div>
                <label for="kode_kategori" class="mb-2 block text-sm font-semibold text-charcoal/80">Kode Kategori</label>
                <input id="kode_kategori" name="kode_kategori" type="text" value="{{ old('kode_kategori') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
                @error('kode_kategori')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="nama_kategori" class="mb-2 block text-sm font-semibold text-charcoal/80">Nama Kategori</label>
                <input id="nama_kategori" name="nama_kategori" type="text" value="{{ old('nama_kategori') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
                @error('nama_kategori')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div class="flex justify-end gap-3">
                <a href="{{ route('kategoris.index') }}" class="rounded-md px-4 py-2.5 text-sm font-semibold text-charcoal/65 hover:bg-sand/30">Batal</a>
                <button type="submit" class="rounded-md bg-forest px-4 py-2.5 text-sm font-semibold text-white hover:bg-forest/90">Simpan</button>
            </div>
        </form>
    </div>
</x-layout>
