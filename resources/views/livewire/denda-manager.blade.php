<div x-data="{
    confirmDelete(id, name) {
        Swal.fire({title:'Hapus denda?', text:name ? 'Denda untuk ' + name + ' akan dihapus.' : 'Data denda akan dihapus permanen.', icon:'warning', showCancelButton:true, confirmButtonColor:'#B3432B', cancelButtonColor:'#706C63', confirmButtonText:'Ya, hapus', cancelButtonText:'Batal'}).then(result => { if (result.isConfirmed) $wire.delete(id) })
    },
    confirmReturn(id, name) { Swal.fire({title:'Konfirmasi pengembalian', text:'Barang transaksi #' + id + ' milik ' + name + ' akan dikembalikan.', icon:'question', showCancelButton:true, confirmButtonColor:'#2F6F4E', confirmButtonText:'Kembalikan', cancelButtonText:'Batal'}).then(result => { if (result.isConfirmed) $wire.kembalikan(id) }) }
}">
    <div class="mb-5 flex items-center justify-between gap-4">
        <div><p class="text-sm text-muted">Catatan denda dari transaksi penyewaan</p></div>
        <button wire:click="$set('form', true)" class="inline-flex items-center gap-2 rounded-xl bg-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-navy-light">
            <span class="text-lg leading-none">+</span> Tambah Denda
        </button>
    </div>

    <div class="mb-5 grid gap-4 sm:grid-cols-2">
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-sm text-muted">Total nilai denda</p><p class="mt-2 font-display text-2xl text-danger">Rp{{ number_format($totalJumlah, 0, ',', '.') }}</p><p class="mt-1 text-xs text-muted">Akumulasi denda yang tercatat</p></div>
        <div class="rounded-2xl border border-border bg-surface p-5 shadow-sm"><p class="text-sm text-muted">Jumlah denda</p><p class="mt-2 font-display text-2xl">{{ number_format($totalDenda) }}</p><p class="mt-1 text-xs text-muted">Catatan denda aktif</p></div>
    </div>

    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-background/70 text-xs uppercase tracking-wide text-muted"><tr><th class="px-5 py-3 text-left font-semibold">Transaksi</th><th class="px-5 py-3 text-left font-semibold">Jumlah</th><th class="px-5 py-3 text-left font-semibold">Alasan</th><th class="px-5 py-3 text-right font-semibold">Aksi</th></tr></thead>
                <tbody class="divide-y divide-border">
                @forelse($dendas as $d)
                    <tr class="transition hover:bg-background/50">
                        <td class="px-5 py-4"><div class="font-semibold text-foreground">#{{ $d->transaksi_sewa_id }}</div><div class="mt-1 text-xs text-muted">{{ $d->transaksi?->namaPenyewa() }}</div></td>
                        <td class="px-5 py-4 font-semibold text-danger">Rp {{ number_format($d->jumlah,0,',','.') }}</td>
                        <td class="max-w-xs px-5 py-4 text-muted">{{ $d->alasan ?: '-' }}</td>
                        <td class="px-5 py-4"><div class="flex justify-end gap-1"><button wire:click="edit({{ $d->id }})" class="rounded-lg px-3 py-2 text-xs font-semibold text-navy hover:bg-navy/10">Edit</button><button x-on:click="confirmDelete({{ $d->id }}, @js($d->transaksi?->namaPenyewa()))" class="rounded-lg px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10">Hapus</button></div></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-5 py-14 text-center"><p class="font-medium text-foreground">Belum ada data denda</p><p class="mt-1 text-sm text-muted">Tambahkan denda dari tombol di atas.</p></td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($dendas->hasPages())<div class="border-t border-border px-5 py-3">{{ $dendas->links('components.pagination') }}</div>@endif
    </div>

    <x-modal name="denda-form" :show="$form" maxWidth="lg">
        <div class="border-b border-border px-6 py-5"><h3 class="font-display text-xl">{{ $dendaId ? 'Edit Denda' : 'Tambah Denda' }}</h3><p class="mt-1 text-sm text-muted">Lengkapi informasi denda transaksi.</p></div>
        <div class="grid gap-4 px-6 py-5">
            <div><label class="mb-2 block text-sm font-semibold">Transaksi</label><select wire:model="transaksi_sewa_id" class="w-full rounded-xl border-border bg-white px-3 py-2.5 text-sm focus:border-navy focus:ring-navy"><option value="">Pilih transaksi</option>@foreach($transaksis as $t)<option value="{{ $t->id }}">#{{ $t->id }} - {{ $t->namaPenyewa() }}</option>@endforeach</select>@error('transaksi_sewa_id')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div><label class="mb-2 block text-sm font-semibold">Jumlah Denda</label><div class="flex items-center rounded-xl border border-border focus-within:border-navy"><span class="pl-3 text-sm text-muted">Rp</span><input type="number" wire:model="jumlah" class="w-full border-0 bg-transparent px-2 py-2.5 text-sm focus:ring-0" placeholder="0"></div>@error('jumlah')<p class="mt-1 text-xs text-danger">{{ $message }}</p>@enderror</div>
            <div><label class="mb-2 block text-sm font-semibold">Alasan</label><textarea wire:model="alasan" rows="3" class="w-full rounded-xl border-border px-3 py-2.5 text-sm focus:border-navy focus:ring-navy" placeholder="Contoh: Keterlambatan pengembalian"></textarea></div>
        </div>
        <div class="flex justify-end gap-2 border-t border-border bg-background/40 px-6 py-4"><button wire:click="$set('form', false)" class="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-muted hover:bg-white">Batal</button><button wire:click="save" wire:loading.attr="disabled" class="rounded-xl bg-navy px-5 py-2.5 text-sm font-semibold text-white hover:bg-navy-light"><span wire:loading.remove wire:target="save">Simpan Denda</span><span wire:loading wire:target="save">Menyimpan...</span></button></div>
    </x-modal>
</div>
