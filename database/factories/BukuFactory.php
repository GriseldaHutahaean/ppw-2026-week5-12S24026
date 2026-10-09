<?php

namespace Database\Factories;

use App\Models\Buku;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Buku>
 */
class BukuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'isbn' => fake()->unique()->numerify('978602########'),
            'judul' => fake()->sentence(fake()->numberBetween(4, 8)),
            'penulis' => fake()->name(),
            'penerbit' => fake()->randomElement([
                'Gramedia Pustaka Utama',
                'Erlangga',
                'Mizan Publika',
                'Bentang Pustaka',
                'Penerbit Andi',
            ]),
            'tahun_terbit' => fake()->numberBetween(2010, 2025),
            'kategori_id' => Kategori::factory(),
            'stok' => fake()->numberBetween(1, 20),
            'sinopsis' => fake()->paragraph(3),
        ];
    }
}
