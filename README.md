# SIPUS-Del

SIPUS-Del (Sistem Informasi Perpustakaan) adalah aplikasi perpustakaan berbasis web yang dikembangkan dengan Laravel. Aplikasi ini membantu pengelolaan data buku dan kategori buku secara terstruktur.

## Fitur Aplikasi

- Manajemen buku: menambah, melihat, mengubah, dan menghapus data buku.
- Manajemen kategori: menambah, melihat, mengubah, dan menghapus data kategori.
- Relasi antara buku dan kategori menggunakan Eloquent.
- Validasi input pada form buku dan kategori.
- Pagination pada daftar buku dan kategori.
- Notifikasi berhasil atau gagal setelah operasi tertentu.
- Tampilan responsif menggunakan Blade dan Tailwind CSS.
- Seeder untuk menyiapkan data contoh kategori dan buku.

Aplikasi saat ini belum menyediakan pencarian, filter, login, transaksi peminjaman, pengembalian, atau perhitungan denda.

## Teknologi yang Digunakan

- Laravel Framework 13.35.0
- PHP 8.3 atau lebih baru sesuai `composer.json` (lingkungan pengembangan terverifikasi menggunakan PHP 8.4.12)
- SQLite sebagai database default pada konfigurasi proyek
- Vite 7
- Tailwind CSS 4
- Laravel Vite Plugin 2
- Axios 1
- Composer dan npm

Versi package PHP utama yang terpasang di lingkungan pengembangan meliputi Laravel Boost 2.10.3, Laravel Pint 1.32.1, Faker 1.24.1, dan PHPUnit 12.5.38.

## Persyaratan Sistem

- PHP 8.3 atau lebih baru
- Composer 2.x
- Node.js dan npm
- SQLite untuk konfigurasi default
- Git

Versi yang digunakan saat pengembangan:

```text
PHP      8.4.12
Composer 2.10.3
Node.js  22.23.2
npm      10.9.8
```

Project tidak menetapkan versi minimum Node.js dan npm secara eksplisit. Versi di atas merupakan versi yang terverifikasi pada lingkungan pengembangan.

## Instalasi dan Konfigurasi

Perintah berikut dijalankan dari PowerShell.

### 1. Clone repository

```powershell
git clone https://github.com/GriseldaHutahaean/ppw-2026-week5-12S24026.git
cd ppw-2026-week5-12S24026
```

### 2. Pasang dependency PHP

```powershell
composer install
```

### 3. Siapkan file environment

```powershell
Copy-Item .env.example .env
```

Buka file `.env`, lalu sesuaikan `APP_NAME`, `APP_URL`, dan konfigurasi database jika diperlukan. Jangan membagikan file `.env` karena dapat berisi konfigurasi rahasia.

### 4. Buat application key

```powershell
php artisan key:generate
```

### 5. Siapkan dependency frontend

```powershell
npm install
npm run build
```

## Menyiapkan Database

Konfigurasi bawaan menggunakan SQLite. Jika file database belum ada, buat file kosong berikut:

```powershell
if (!(Test-Path database/database.sqlite)) {
    New-Item database/database.sqlite -ItemType File | Out-Null
}
```

Jalankan migration:

```powershell
php artisan migrate
```

Migration membuat tabel bawaan Laravel, tabel `kategoris`, dan tabel `bukus`. Tabel `bukus` memiliki relasi foreign key ke `kategoris` dan kategori tidak dapat dihapus jika masih digunakan oleh buku.

Seeder bersifat opsional dan digunakan untuk menyiapkan data contoh:

```powershell
php artisan db:seed
```

Seeder menyiapkan minimal lima kategori dan melengkapi data buku hingga minimal 20 data. Jangan menggunakan `migrate:fresh`, `db:wipe`, atau perintah reset lain pada database yang berisi data penting.

## Menjalankan Aplikasi

Jalankan server development dengan perintah berikut:

```powershell
php artisan serve
```

Buka alamat berikut pada browser:

```text
http://127.0.0.1:8000
```

Terminal tempat `php artisan serve` dijalankan harus tetap terbuka selama aplikasi digunakan. Halaman utama saat ini menampilkan view welcome bawaan Laravel, sedangkan fitur utama dapat diakses melalui:

- `http://127.0.0.1:8000/bukus`
- `http://127.0.0.1:8000/kategoris`

## Struktur Direktori

```text
app/
├── Http/Controllers/       Controller buku dan kategori
├── Http/Requests/           Form Request untuk validasi
└── Models/                  Model Buku, Kategori, dan User

database/
├── factories/               Factory untuk data contoh
├── migrations/              Struktur tabel database
└── seeders/                 Seeder data awal

resources/views/
├── bukus/                   View CRUD buku
├── kategoris/               View CRUD kategori
└── components/              Komponen layout Blade

routes/
└── web.php                  Route halaman web dan resource CRUD
```

## Validasi dan Relasi Data

### Validasi kategori

- `kode_kategori` wajib diisi, berupa string, maksimal 255 karakter, dan harus unik.
- `nama_kategori` wajib diisi dan berupa string.
- Saat update, validasi unik mengabaikan kategori yang sedang diedit.

### Validasi buku

- `isbn` wajib diisi, berupa string, maksimal 20 karakter, dan harus unik.
- `judul` wajib diisi dan minimal 5 karakter.
- `penulis` dan `penerbit` wajib diisi.
- `tahun_terbit` harus berupa bilangan bulat dari tahun 1000 sampai tahun berjalan.
- `kategori_id` wajib mengacu pada kategori yang tersedia.
- `stok` harus berupa bilangan bulat minimal 0.
- `sinopsis` boleh kosong.
- Saat update, validasi ISBN unik mengabaikan buku yang sedang diedit.

### Relasi Eloquent

- `Kategori::bukus()` menggunakan relasi `hasMany`.
- `Buku::kategori()` menggunakan relasi `belongsTo`.
- Daftar buku menggunakan eager loading `Buku::with('kategori')` agar nama kategori dapat ditampilkan tanpa query relasi berulang.

## Pengembang

**Griselda Hutahaean**

Repository: [ppw-2026-week5-12S24026](https://github.com/GriseldaHutahaean/ppw-2026-week5-12S24026)
