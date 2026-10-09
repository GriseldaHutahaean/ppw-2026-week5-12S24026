<?php

namespace Database\Seeders;

use App\Models\Buku;
use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => 'password'],
        );

        $kategori = [
            ['kode_kategori' => 'KAT-FIK', 'nama_kategori' => 'Fiksi'],
            ['kode_kategori' => 'KAT-ILM', 'nama_kategori' => 'Ilmu Pengetahuan'],
            ['kode_kategori' => 'KAT-TEK', 'nama_kategori' => 'Teknologi'],
            ['kode_kategori' => 'KAT-SEJ', 'nama_kategori' => 'Sejarah'],
            ['kode_kategori' => 'KAT-PEN', 'nama_kategori' => 'Pendidikan'],
        ];

        foreach ($kategori as $data) {
            Kategori::firstOrCreate(
                ['kode_kategori' => $data['kode_kategori']],
                ['nama_kategori' => $data['nama_kategori']],
            );
        }

        $jumlahBuku = max(0, 20 - Buku::count());
        $kategoriIds = Kategori::query()->pluck('id')->all();

        if ($jumlahBuku > 0) {
            Buku::factory()
                ->count($jumlahBuku)
                ->state(fn (): array => [
                    'kategori_id' => fake()->randomElement($kategoriIds),
                ])
                ->create();
        }
    }
}
