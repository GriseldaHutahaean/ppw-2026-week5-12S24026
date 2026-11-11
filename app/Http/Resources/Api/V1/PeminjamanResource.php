<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Peminjaman
 */
class PeminjamanResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'kode_pinjam' => $this->kode_pinjam,
            'buku_id' => $this->buku_id,
            'nim_peminjam' => $this->nim_peminjam,
            'nama_peminjam' => $this->nama_peminjam,
            'tanggal_pinjam' => $this->tanggal_pinjam?->toDateString(),
            'tanggal_jatuh_tempo' => $this->tanggal_jatuh_tempo?->toDateString(),
            'tanggal_kembali' => $this->tanggal_kembali?->toDateString(),
            'status' => $this->status,
            'buku' => $this->whenLoaded('buku', fn (): array => [
                'id' => $this->buku->id,
                'judul' => $this->buku->judul,
                'isbn' => $this->buku->isbn,
            ]),
            '_links' => [
                'self' => ['href' => '/api/v1/peminjamans/'.$this->id, 'method' => 'GET'],
            ],
        ];

        if ($this->status === 'dipinjam') {
            $data['_links']['kembalikan_buku'] = [
                'href' => '/api/v1/peminjamans/'.$this->id.'/kembalikan',
                'method' => 'PATCH',
            ];
        }

        return $data;
    }
}
