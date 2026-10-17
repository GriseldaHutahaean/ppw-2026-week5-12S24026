<x-layout title="Peminjaman | SIPUS-Del">
    <div class="mb-9 border-b border-border-warm pb-8">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-brass">Sirkulasi</p>
        <h1 class="mt-3 text-4xl font-semibold tracking-tight text-charcoal sm:text-5xl">Daftar Peminjaman</h1>
        <p class="mt-3 text-sm text-muted">{{ $peminjamans->total() }} transaksi tercatat.</p>
    </div>

    <div class="overflow-hidden border border-border-warm bg-white">
        <div class="overflow-x-auto">
            <table class="min-w-[1000px] divide-y divide-border-warm text-left text-sm">
                <caption class="sr-only">Daftar peminjaman buku SIPUS-Del</caption>
                <thead class="bg-sand text-xs uppercase tracking-[0.12em] text-muted">
                    <tr>
                        <th scope="col" class="px-5 py-4 font-semibold">Kode</th>
                        <th scope="col" class="min-w-56 px-5 py-4 font-semibold">Buku</th>
                        <th scope="col" class="px-5 py-4 font-semibold">NIM</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Peminjam</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Tgl Pinjam</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Jatuh Tempo</th>
                        <th scope="col" class="px-5 py-4 font-semibold">Tgl Kembali</th>
                        <th scope="col" class="px-5 py-4 text-center font-semibold">Status</th>
                        <th scope="col" class="px-5 py-4 text-right font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border-warm/80">
                    @forelse ($peminjamans as $p)
                        <tr class="odd:bg-sand/35 hover:bg-[#F5F3FF]">
                            <td class="whitespace-nowrap px-5 py-4 font-mono text-xs text-muted">{{ $p->kode_pinjam }}</td>
                            <td class="min-w-56 px-5 py-4 font-semibold text-charcoal">{{ $p->buku->judul }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $p->nim_peminjam }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $p->nama_peminjam }}</td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $p->tanggal_pinjam->format('d M Y') }}</td>
                            <td class="whitespace-nowrap px-5 py-4 {{ $p->status === 'dipinjam' && $p->tanggal_jatuh_tempo->isBefore(today()) ? 'font-semibold text-red-700' : 'text-charcoal/80' }}">
                                {{ $p->tanggal_jatuh_tempo->format('d M Y') }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-4 text-charcoal/80">{{ $p->tanggal_kembali?->format('d M Y') ?? '-' }}</td>
                            <td class="px-5 py-4 text-center">
                                @if ($p->status === 'dipinjam')
                                    <span class="rounded bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-800">Dipinjam</span>
                                @else
                                    <span class="rounded bg-green-50 px-2 py-1 text-xs font-semibold text-green-800">Dikembalikan</span>
                                @endif
                            </td>
                            <td class="px-5 py-4 text-right">
                                @if ($p->status === 'dipinjam')
                                    <form action="{{ route('peminjamans.kembali', $p->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded-md border border-forest/30 px-3 py-2 text-xs font-semibold text-forest hover:bg-sand"
                                                onclick="return confirm('Kembalikan buku ini?')">Kembalikan</button>
                                    </form>
                                @else
                                    <span class="text-xs text-muted">Selesai</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-5 py-14 text-center text-muted">Belum ada transaksi peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($peminjamans->hasPages())
        <div class="mt-6">{{ $peminjamans->links() }}</div>
    @endif
</x-layout>
