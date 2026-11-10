<div x-data="{ confirmDelete(id, name) { Swal.fire({title:'Hapus transaksi?', text:'Transaksi #' + id + ' milik ' + name + ' akan dihapus.', icon:'warning', showCancelButton:true, confirmButtonColor:'#B3432B', confirmButtonText:'Ya, hapus', cancelButtonText:'Batal'}).then(r => { if(r.isConfirmed) $wire.delete(id) }) }, confirmReturn(id, name) { Swal.fire({title:'Konfirmasi pengembalian', text:'Barang transaksi #' + id + ' milik ' + name + ' akan dikembalikan.', icon:'question', showCancelButton:true, confirmButtonColor:'#2F6F4E', confirmButtonText:'Kembalikan', cancelButtonText:'Batal'}).then(r => { if(r.isConfirmed) $wire.kembalikan(id) }) } }">
    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-2" role="group" aria-label="Filter status transaksi">
            @foreach(['' => 'Semua', 'berjalan' => 'Berjalan', 'terlambat' => 'Terlambat', 'selesai' => 'Selesai'] as $key => $label)
                <button wire:click="$set('filterStatus', '{{ $key }}')" class="rounded-lg border px-3.5 py-2 text-sm font-semibold transition {{ $filterStatus === $key ? 'border-navy bg-navy text-white' : 'border-border bg-surface text-muted hover:border-navy hover:text-navy' }}">{{ $label }}</button>
            @endforeach
        </div>
        <button wire:click="$set('form', true)" class="rounded-xl bg-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-navy-light">+ Tambah Transaksi</button>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-background/70 text-xs uppercase tracking-wide text-muted"><tr><th class="px-5 py-3 text-left">Transaksi</th><th class="px-5 py-3 text-left">Penyewa</th><th class="px-5 py-3 text-left">Periode</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-border">
                    @forelse($transaksis as $t)
                        @php($terlambat = $t->status === 'berjalan' && $t->tanggal_kembali_rencana?->lt(today()))
                        <tr class="hover:bg-background/50">
                            <td class="px-5 py-4"><div class="font-semibold">#{{ $t->id }}</div><div class="mt-1 max-w-xs truncate text-xs text-muted">{{ $t->barang->map(fn($i) => $i->barang?->nama_barang)->filter()->join(', ') ?: '-' }}</div></td>
                            <td class="px-5 py-4 font-medium">{{ $t->namaPenyewa() }}<div class="mt-1 text-xs text-muted">{{ $t->no_hp_manual ?: $t->penyewa?->no_hp ?: '' }}</div></td>
                            <td class="px-5 py-4 text-muted"><div>{{ $t->tanggal_sewa?->format('d/m/Y') ?: '-' }}</div><div class="text-xs">s.d. {{ $t->tanggal_kembali_rencana?->format('d/m/Y') ?: '-' }}</div></td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $terlambat ? 'bg-danger/10 text-danger' : ($t->status === 'selesai' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning') }}">{{ $terlambat ? 'Terlambat' : ucfirst($t->status) }}</span></td>
                            <td class="px-5 py-4"><div class="flex justify-end">@if($t->status === 'berjalan')<button x-on:click="confirmReturn({{ $t->id }}, @js($t->namaPenyewa()))" class="rounded-lg px-3 py-2 text-xs font-semibold text-success hover:bg-success/10">Kembalikan</button>@endif<button x-on:click="confirmDelete({{ $t->id }}, @js($t->namaPenyewa()))" class="rounded-lg px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10">Hapus</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-14 text-center text-muted">Tidak ada transaksi untuk filter ini.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($transaksis->hasPages())<div class="border-t border-border px-5 py-3">{{ $transaksis->links('components.pagination') }}</div>@endif
    </div>

    <x-modal name="transaksi-form" :show="$form" maxWidth="4xl">
        <div class="border-b border-border px-6 py-5"><h3 class="font-display text-xl">Tambah Transaksi Sewa</h3><p class="mt-1 text-sm text-muted">Isi data penyewa dan pilih barang yang tersedia.</p></div>
        <div class="grid max-h-[65vh] gap-4 overflow-y-auto px-6 py-5 md:grid-cols-2">
            <label class="text-sm font-semibold md:col-span-2">Nama penyewa<input wire:model="nama_penyewa_manual" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Masukkan nama penyewa">@error('nama_penyewa_manual')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">No. HP<input wire:model="no_hp_manual" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Nomor telepon"></label>
            <label class="text-sm font-semibold">Jenis jaminan<select wire:model="jenis_jaminan" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal"><option value="">Pilih jenis jaminan</option><option value="KTP">KTP</option><option value="SIM">SIM</option><option value="KTM">KTM</option><option value="Deposit">Deposit</option></select>@error('jenis_jaminan')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Tanggal sewa<input type="date" wire:model="tanggal_sewa" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@error('tanggal_sewa')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Tanggal kembali<input type="date" wire:model="tanggal_kembali_rencana" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@error('tanggal_kembali_rencana')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Harga sewa<input type="number" wire:model="harga_sewa" min="0" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@error('harga_sewa')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Foto peminjam<input wire:model="foto_peminjam" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="URL foto (opsional)"></label>
            <label class="text-sm font-semibold md:col-span-2">Keterangan<textarea wire:model="keterangan" rows="2" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal"></textarea></label>

            <div class="md:col-span-2">
                <div class="mb-2 flex items-center justify-between gap-2"><label for="barang-search" class="text-sm font-semibold">Pilih barang tersedia</label><button type="button" wire:click="clearBarangSelection" class="text-xs font-semibold text-muted hover:text-navy">Kosongkan pilihan</button></div>
                <input id="barang-search" type="search" wire:model.live.debounce.250ms="barangSearch" class="mb-3 w-full rounded-xl px-3 py-2.5 text-sm" placeholder="Cari nama atau kode barang">
                @if($selectedBarangs->isNotEmpty())
                    <div class="mb-3 flex flex-wrap gap-2">
                        @foreach($selectedBarangs as $selectedBarang)
                            <span class="inline-flex items-center gap-2 rounded-full bg-navy/10 px-3 py-1.5 text-xs font-semibold text-navy">{{ $selectedBarang->nama_barang }} · {{ $selectedBarang->kode_barang }}<button type="button" wire:click="removeBarang({{ $selectedBarang->id }})" class="text-navy/70 hover:text-danger" aria-label="Hapus {{ $selectedBarang->nama_barang }} dari pilihan">×</button></span>
                        @endforeach
                    </div>
                @endif
                <div class="max-h-56 space-y-2 overflow-y-auto rounded-xl border border-border bg-background/50 p-2">
                    @forelse($barangs as $b)
                        <label wire:key="barang-option-{{ $b->id }}" class="flex cursor-pointer items-center gap-3 rounded-lg border border-border bg-white p-3 transition hover:border-navy">
                            <input type="checkbox" wire:model="barangIds" value="{{ $b->id }}" class="h-4 w-4 rounded border-border text-navy focus:ring-navy">
                            <span class="min-w-0 flex-1"><span class="block truncate text-sm font-semibold">{{ $b->nama_barang }}</span><span class="text-xs text-muted">{{ $b->kode_barang }} · Ukuran {{ $b->ukuran ?: '-' }} · Rp{{ number_format($b->harga_satuan, 0, ',', '.') }}</span></span>
                            <span class="text-xs font-medium text-success">Tersedia</span>
                        </label>
                    @empty
                        <p class="rounded-lg p-4 text-center text-sm text-muted">{{ $barangSearch !== '' ? 'Barang tidak ditemukan.' : 'Tidak ada barang tersedia.' }}</p>
                    @endforelse
                </div>
                @error('barangIds')<p class="mt-2 text-xs text-danger">{{ $message }}</p>@enderror
            </div>
        </div>
        <div class="flex justify-end gap-2 border-t border-border bg-background/40 px-6 py-4"><button wire:click="$set('form', false)" class="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-muted">Batal</button><button wire:click="save" wire:loading.attr="disabled" class="rounded-xl bg-navy px-5 py-2.5 text-sm font-semibold text-white"><span wire:loading.remove wire:target="save">Simpan Transaksi</span><span wire:loading wire:target="save">Menyimpan...</span></button></div>
    </x-modal>
</div>
