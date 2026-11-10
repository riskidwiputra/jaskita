<div class="space-y-5">
    <div class="grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl border border-success/20 bg-success/10 p-5"><p class="mb-1 text-sm text-success/80">Total pemasukan</p><p class="font-display text-xl text-success">Rp{{ number_format($masuk, 0, ',', '.') }}</p></div>
        <div class="rounded-2xl border border-danger/20 bg-danger/10 p-5"><p class="mb-1 text-sm text-danger/80">Total pengeluaran</p><p class="font-display text-xl text-danger">Rp{{ number_format($keluar, 0, ',', '.') }}</p></div>
        <div class="rounded-2xl bg-navy p-5"><p class="mb-1 text-sm text-white/70">Saldo bersih</p><p class="font-display text-xl text-white">Rp{{ number_format($masuk - $keluar, 0, ',', '.') }}</p></div>
    </div>

    <section class="rounded-2xl border border-border bg-surface p-4 shadow-sm">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <div><h2 class="font-display text-xl">Riwayat kas</h2><p class="mt-1 text-sm text-muted">Pemasukan dan pengeluaran dikelompokkan sesuai periode.</p></div>
            <div class="flex gap-2" role="group" aria-label="Kelompok periode buku kas">
                @foreach(['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                    <button wire:click="$set('tab', '{{ $key }}')" class="rounded-lg border px-4 py-2 text-sm font-semibold transition {{ $tab === $key ? 'border-navy bg-navy text-white' : 'border-border bg-white text-muted hover:border-navy hover:text-navy' }}">{{ $label }}</button>
                @endforeach
            </div>
        </div>

        @if($groups->isEmpty())
            <div class="rounded-xl border border-dashed border-border px-5 py-12 text-center text-sm text-muted">Belum ada transaksi kas tercatat.</div>
        @else
            <div class="space-y-4">
                @foreach($groups as $group)
                    <section class="overflow-hidden rounded-xl border border-border bg-white">
                        <div class="flex items-center justify-between gap-3 border-b border-border bg-background/70 px-4 py-3">
                            <h3 class="text-sm font-bold">{{ $group['title'] }}</h3>
                            <div class="flex gap-3 text-xs">
                                <span class="font-semibold text-success">+Rp{{ number_format($group['entries']->where('jenis', 'masuk')->sum('jumlah'), 0, ',', '.') }}</span>
                                <span class="font-semibold text-danger">−Rp{{ number_format($group['entries']->where('jenis', 'keluar')->sum('jumlah'), 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="divide-y divide-border">
                            @foreach($group['entries'] as $entry)
                                <div class="flex flex-col gap-2 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <span class="mr-2 inline-flex rounded-md px-2 py-0.5 text-xs font-semibold {{ $entry['jenis'] === 'masuk' ? 'bg-success/10 text-success' : 'bg-danger/10 text-danger' }}">{{ $entry['kategori'] }}</span>
                                        <span class="text-sm">{{ $entry['keterangan'] }}</span>
                                        <p class="mt-1 text-xs text-muted">{{ \Carbon\Carbon::parse($entry['tanggal'])->locale('id')->translatedFormat('d M Y') }}</p>
                                    </div>
                                    <span class="shrink-0 text-sm font-semibold {{ $entry['jenis'] === 'masuk' ? 'text-success' : 'text-danger' }}">{{ $entry['jenis'] === 'masuk' ? '+' : '−' }}Rp{{ number_format($entry['jumlah'], 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        @endif
    </section>
</div>
