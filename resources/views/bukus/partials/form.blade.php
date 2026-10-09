<div class="grid gap-6 sm:grid-cols-2">
    <div>
        <label for="isbn" class="mb-2 block text-sm font-semibold text-charcoal/80">ISBN</label>
        <input id="isbn" name="isbn" type="text" value="{{ old('isbn', $buku->isbn ?? '') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('isbn')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="judul" class="mb-2 block text-sm font-semibold text-charcoal/80">Judul</label>
        <input id="judul" name="judul" type="text" value="{{ old('judul', $buku->judul ?? '') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('judul')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="penulis" class="mb-2 block text-sm font-semibold text-charcoal/80">Penulis</label>
        <input id="penulis" name="penulis" type="text" value="{{ old('penulis', $buku->penulis ?? '') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('penulis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="penerbit" class="mb-2 block text-sm font-semibold text-charcoal/80">Penerbit</label>
        <input id="penerbit" name="penerbit" type="text" value="{{ old('penerbit', $buku->penerbit ?? '') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('penerbit')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="tahun_terbit" class="mb-2 block text-sm font-semibold text-charcoal/80">Tahun Terbit</label>
        <input id="tahun_terbit" name="tahun_terbit" type="number" min="1000" max="{{ now()->year }}" value="{{ old('tahun_terbit', $buku->tahun_terbit ?? '') }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('tahun_terbit')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label for="stok" class="mb-2 block text-sm font-semibold text-charcoal/80">Stok</label>
        <input id="stok" name="stok" type="number" min="0" value="{{ old('stok', $buku->stok ?? 0) }}" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
        @error('stok')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label for="kategori_id" class="mb-2 block text-sm font-semibold text-charcoal/80">Kategori</label>
        <select id="kategori_id" name="kategori_id" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest" required>
            <option value="">Pilih kategori</option>
            @foreach ($kategoris as $kategori)
                <option value="{{ $kategori->id }}" @selected((string) old('kategori_id', $buku->kategori_id ?? '') === (string) $kategori->id)>{{ $kategori->nama_kategori }}</option>
            @endforeach
        </select>
        @error('kategori_id')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div class="sm:col-span-2">
        <label for="sinopsis" class="mb-2 block text-sm font-semibold text-charcoal/80">Sinopsis <span class="font-normal text-charcoal/45">(opsional)</span></label>
        <textarea id="sinopsis" name="sinopsis" rows="5" class="w-full rounded-md border-border-warm bg-ivory px-4 py-3 text-sm focus:border-forest focus:ring-forest">{{ old('sinopsis', $buku->sinopsis ?? '') }}</textarea>
        @error('sinopsis')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
