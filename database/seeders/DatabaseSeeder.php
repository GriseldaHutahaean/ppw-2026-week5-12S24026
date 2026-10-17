<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'pustakawan@del.ac.id'],
            ['name' => 'Pustakawan Del', 'password' => Hash::make('password123')],
        );

        $kategoris = collect([
            ['kode_kategori' => 'KAT-FIK', 'nama_kategori' => 'Fiksi'],
            ['kode_kategori' => 'KAT-ILM', 'nama_kategori' => 'Ilmu Pengetahuan'],
            ['kode_kategori' => 'KAT-TEK', 'nama_kategori' => 'Teknologi'],
            ['kode_kategori' => 'KAT-SEJ', 'nama_kategori' => 'Sejarah'],
            ['kode_kategori' => 'KAT-PEN', 'nama_kategori' => 'Pendidikan'],
        ])->mapWithKeys(function (array $data): array {
            $kategori = Kategori::updateOrCreate(
                ['kode_kategori' => $data['kode_kategori']],
                ['nama_kategori' => $data['nama_kategori']],
            );

            return [$data['kode_kategori'] => $kategori];
        });

        $bukuHabis = Buku::updateOrCreate(
            ['isbn' => '9786022911234'],
            [
                'judul' => 'Pemrograman Web Modern',
                'penulis' => 'Rizky Pratama',
                'penerbit' => 'Penerbit Andi',
                'tahun_terbit' => 2024,
                'kategori_id' => $kategoris['KAT-TEK']->id,
                'stok' => 0,
                'sinopsis' => 'Pengantar pengembangan aplikasi web modern.',
            ],
        );

        $bukuTersedia = Buku::updateOrCreate(
            ['isbn' => '9786022915678'],
            [
                'judul' => 'Dasar-Dasar Basis Data',
                'penulis' => 'Sinta Lestari',
                'penerbit' => 'Erlangga',
                'tahun_terbit' => 2023,
                'kategori_id' => $kategoris['KAT-ILM']->id,
                'stok' => 1,
                'sinopsis' => 'Konsep dasar perancangan dan pengelolaan basis data.',
            ],
        );

        foreach ([
            ['isbn' => '9786022919014', 'judul' => 'Sejarah Nusantara', 'penulis' => 'Maya Sari', 'penerbit' => 'Mizan Publika', 'tahun_terbit' => 2022, 'kategori_id' => $kategoris['KAT-SEJ']->id, 'stok' => 4],
            ['isbn' => '9786022913450', 'judul' => 'Strategi Belajar Efektif', 'penulis' => 'Doni Simanjuntak', 'penerbit' => 'Gramedia Pustaka Utama', 'tahun_terbit' => 2021, 'kategori_id' => $kategoris['KAT-PEN']->id, 'stok' => 5],
            ['isbn' => '9786022917899', 'judul' => 'Cerita dari Danau Toba', 'penulis' => 'Nadia Hutapea', 'penerbit' => 'Bentang Pustaka', 'tahun_terbit' => 2020, 'kategori_id' => $kategoris['KAT-FIK']->id, 'stok' => 3],
        ] as $data) {
            Buku::updateOrCreate(['isbn' => $data['isbn']], $data + ['sinopsis' => null]);
        }

        Peminjaman::updateOrCreate(
            ['kode_pinjam' => 'PJM-SEED001'],
            [
                'buku_id' => $bukuHabis->id,
                'nim_peminjam' => '12S24001',
                'nama_peminjam' => 'Mahasiswa Del',
                'tanggal_pinjam' => now()->subDays(2)->toDateString(),
                'tanggal_jatuh_tempo' => now()->addDays(5)->toDateString(),
                'tanggal_kembali' => null,
                'status' => 'dipinjam',
            ],
        );

        Peminjaman::updateOrCreate(
            ['kode_pinjam' => 'PJM-SEED002'],
            [
                'buku_id' => $bukuTersedia->id,
                'nim_peminjam' => '12S24002',
                'nama_peminjam' => 'Alumni Del',
                'tanggal_pinjam' => now()->subDays(10)->toDateString(),
                'tanggal_jatuh_tempo' => now()->subDays(3)->toDateString(),
                'tanggal_kembali' => now()->subDays(2)->toDateString(),
                'status' => 'dikembalikan',
            ],
        );
    }
}
