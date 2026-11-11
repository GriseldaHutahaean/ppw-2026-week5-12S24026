<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Buku;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Buku
 */
class BukuResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'isbn' => $this->isbn,
            'judul' => $this->judul,
            'penulis' => $this->penulis,
            'penerbit' => $this->penerbit,
            'tahun_terbit' => $this->tahun_terbit,
            'kategori' => $this->whenLoaded('kategori', fn (): array => [
                'id' => $this->kategori->id,
                'kode_kategori' => $this->kategori->kode_kategori,
                'nama_kategori' => $this->kategori->nama_kategori,
            ]),
            'kategori_id' => $this->kategori_id,
            'stok' => $this->stok,
            'sinopsis' => $this->sinopsis,
            'peminjamans_count' => $this->whenCounted('peminjamans'),
            'riwayat_peminjaman' => $this->whenLoaded(
                'peminjamans',
                fn () => PeminjamanResource::collection($this->peminjamans),
            ),
            '_links' => $this->links(),
        ];
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function links(): array
    {
        $links = [
            'self' => ['href' => '/api/v1/bukus/'.$this->id, 'method' => 'GET'],
            'collection' => ['href' => '/api/v1/bukus', 'method' => 'GET'],
        ];

        if ($this->stok > 0) {
            $links['pinjam_buku'] = [
                'href' => '/api/v1/bukus/'.$this->id.'/peminjamans',
                'method' => 'POST',
            ];
        }

        return $links;
    }
}
