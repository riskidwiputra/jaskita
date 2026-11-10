<div class="space-y-6">
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-xs font-medium text-muted">Barang tersedia</p><p class="mt-2 font-display text-2xl">{{ number_format($tersedia) }}</p><p class="mt-1 text-xs text-success">Siap disewakan</p></div>
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-xs font-medium text-muted">Sedang disewa</p><p class="mt-2 font-display text-2xl">{{ number_format($disewa) }}</p><p class="mt-1 text-xs text-warning">{{ $jatuhTempo }} melewati tanggal kembali</p></div>
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-xs font-medium text-muted">Total transaksi sewa</p><p class="mt-2 font-display text-2xl">{{ number_format($totalPernahSewa) }}</p><p class="mt-1 text-xs text-muted">Sepanjang waktu</p></div>
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-xs font-medium text-muted">Total penyewa</p><p class="mt-2 font-display text-2xl">{{ number_format($totalPenyewa) }}</p><p class="mt-1 text-xs text-muted">Pelanggan terdaftar</p></div>
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-xs font-medium text-muted">Total denda tercatat</p><p class="mt-2 font-display text-xl">Rp{{ number_format($totalDenda, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">Nilai akumulasi</p></div>
    </div>

    <section class="rounded-2xl border border-border bg-surface p-5 shadow-sm sm:p-6">
        <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div><h2 class="font-display text-xl">Grafik pendapatan</h2><p class="mt-1 text-sm text-muted">Ringkasan pendapatan bersih enam bulan terakhir.</p>@if($trend !== null)<p class="mt-1 text-xs font-semibold {{ $trend > 0 ? 'text-success' : ($trend < 0 ? 'text-danger' : 'text-muted') }}">{{ $trend > 0 ? '+' : '' }}{{ $trend }}% dibanding bulan lalu</p>@else<p class="mt-1 text-xs text-muted">Belum ada data bulan lalu sebagai pembanding.</p>@endif</div>
            <div class="flex flex-wrap gap-2" role="group" aria-label="Filter periode pendapatan">
                @foreach(['hari_ini' => 'Hari ini', 'bulan_ini' => 'Bulan ini', 'tahun_ini' => 'Tahun ini', 'keseluruhan' => 'Keseluruhan'] as $key => $label)
                    <button wire:click="$set('revenuePeriod', '{{ $key }}')" class="rounded-lg border px-3 py-2 text-xs font-semibold transition {{ $revenuePeriod === $key ? 'border-navy bg-navy text-white' : 'border-border bg-white text-muted hover:border-navy hover:text-navy' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        <div class="grid gap-5 lg:grid-cols-[minmax(0,1.7fr)_minmax(240px,0.8fr)]">
            <div class="min-w-0 rounded-xl bg-background/70 p-3 sm:p-4">
                @php($chartMin = min(0, (int) $chart->min('total')))
                @php($chartMax = max(1, (int) $chart->max('total')))
                @php($chartRange = max(1, $chartMax - $chartMin))
                <svg viewBox="0 0 600 170" role="img" aria-label="Grafik pendapatan bersih enam bulan terakhir" class="h-52 w-full overflow-visible">
                    <line x1="30" y1="26" x2="570" y2="26" stroke="#E4E0D8" stroke-dasharray="4 5" />
                    <line x1="30" y1="79" x2="570" y2="79" stroke="#E4E0D8" stroke-dasharray="4 5" />
                    <line x1="30" y1="132" x2="570" y2="132" stroke="#E4E0D8" />
                    <path d="{{ $chartPath }}" fill="none" stroke="#1F2A44" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" />
                    @foreach($chart as $index => $point)
                        @php($x = 30 + ($index * 540 / 5))
                        @php($y = 132 - (($point['total'] - $chartMin) / $chartRange * 106))
                        <circle cx="{{ $x }}" cy="{{ $y }}" r="4" fill="#9C6B2E" stroke="#fff" stroke-width="2" />
                        <text x="{{ $x }}" y="158" text-anchor="middle" fill="#706C63" font-size="11">{{ $point['label'] }}</text>
                    @endforeach
                </svg>
            </div>

            <div class="grid gap-3 sm:grid-cols-3 lg:grid-cols-1">
                <div class="rounded-xl border border-border bg-white p-4"><p class="text-xs text-muted">Pendapatan kotor · {{ ['hari_ini' => 'hari ini', 'bulan_ini' => 'bulan ini', 'tahun_ini' => 'tahun ini', 'keseluruhan' => 'keseluruhan'][$revenuePeriod] ?? 'bulan ini' }}</p><p class="mt-2 font-display text-xl">Rp{{ number_format($grossRevenue, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">Sewa + denda</p></div>
                <div class="rounded-xl border border-border bg-white p-4"><p class="text-xs text-muted">Pengeluaran operasional</p><p class="mt-2 font-display text-xl text-danger">Rp{{ number_format($operatingExpenses, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">Tidak termasuk pembelian modal</p></div>
                <div class="rounded-xl bg-navy p-4 text-white shadow-sm"><p class="text-xs text-white/70">Pendapatan bersih</p><p class="mt-2 font-display text-xl">Rp{{ number_format($netRevenue, 0, ',', '.') }}</p><p class="mt-1 text-xs text-white/70">Kotor dikurangi pengeluaran</p></div>
            </div>
        </div>
    </section>

    <section class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
        <div class="border-b border-border px-5 py-4"><h2 class="text-sm font-semibold">Transaksi terbaru</h2></div>
        @if($terbaru->isEmpty())
            <div class="px-5 py-12 text-center text-sm text-muted">Belum ada transaksi.</div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-background/60 text-xs uppercase tracking-wide text-muted"><tr><th class="px-5 py-3 text-left">Penyewa</th><th class="px-5 py-3 text-left">Tgl sewa</th><th class="px-5 py-3 text-left">Tgl kembali</th><th class="px-5 py-3 text-left">Status</th></tr></thead>
                    <tbody class="divide-y divide-border">
                        @foreach($terbaru as $t)
                            @php($terlambat = $t->status === 'berjalan' && $t->tanggal_kembali_rencana?->lt(today()))
                            <tr class="hover:bg-background/50">
                                <td class="px-5 py-3.5 font-medium">{{ $t->namaPenyewa() }}</td>
                                <td class="px-5 py-3.5 text-muted">{{ $t->tanggal_sewa?->format('d/m/Y') ?: '-' }}</td>
                                <td class="px-5 py-3.5 text-muted">{{ $t->tanggal_kembali_rencana?->format('d/m/Y') ?: '-' }}</td>
                                <td class="px-5 py-3.5"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $terlambat ? 'bg-danger/10 text-danger' : ($t->status === 'selesai' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning') }}">{{ $terlambat ? 'Terlambat' : ucfirst($t->status) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </section>
</div>
