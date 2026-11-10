<div x-data="{ confirmDelete(id, name) { Swal.fire({title:'Hapus barang?', text:name + ' akan dihapus permanen.', icon:'warning', showCancelButton:true, confirmButtonColor:'#B3432B', confirmButtonText:'Ya, hapus', cancelButtonText:'Batal'}).then(r => { if(r.isConfirmed) $wire.delete(id) }) } }">
    <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
        <p class="text-sm text-muted">Cari dan saring inventaris berdasarkan ukuran, jenis, atau pembeli.</p>
        <button wire:click="$set('form', true)" class="rounded-xl bg-navy px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-navy-light">+ Tambah Barang</button>
    </div>
    <div class="mb-4 grid gap-3 rounded-2xl border border-border bg-surface p-4 shadow-sm sm:grid-cols-2 xl:grid-cols-4">
        <input wire:model.live.debounce.300ms="search" class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm shadow-sm focus:border-navy focus:ring-navy" placeholder="Cari nama atau kode barang">
        <select wire:model.live="filterUkuran" class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm"><option value="">Semua ukuran</option>@foreach($ukuranOptions as $ukuranOption)<option value="{{ $ukuranOption }}">{{ $ukuranOption }}</option>@endforeach</select>
        <select wire:model.live="filterType" class="w-full rounded-xl border border-border bg-white px-3 py-2.5 text-sm"><option value="">Semua tipe</option>@foreach($typeOptions as $typeOption)<option value="{{ $typeOption }}">{{ $typeOption }}</option>@endforeach</select>
        <div class="flex gap-2">
            <select wire:model.live="filterPembeli" class="min-w-0 flex-1 rounded-xl border border-border bg-white px-3 py-2.5 text-sm"><option value="">Semua pembeli</option>@foreach($pembeliOptions as $pembeliOption)<option value="{{ $pembeliOption }}">{{ $pembeliOption }}</option>@endforeach</select>
            <button wire:click="clearFilters" class="rounded-xl border border-border px-3 text-xs font-semibold text-muted hover:bg-background">Reset</button>
        </div>
    </div>
    <div class="overflow-hidden rounded-2xl border border-border bg-surface shadow-sm">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-background/70 text-xs uppercase tracking-wide text-muted"><tr><th class="px-5 py-3 text-left">Barang</th><th class="px-5 py-3 text-left">Ukuran</th><th class="px-5 py-3 text-left">Dibeli oleh</th><th class="px-5 py-3 text-left">Stok</th><th class="px-5 py-3 text-left">Harga</th><th class="px-5 py-3 text-left">Status</th><th class="px-5 py-3 text-right">Aksi</th></tr></thead>
                <tbody class="divide-y divide-border">
                    @forelse($barangs as $b)
                        <tr class="hover:bg-background/50">
                            <td class="px-5 py-4"><div class="font-semibold">{{ $b->nama_barang }}</div><div class="mt-1 text-xs text-muted">{{ $b->kode_barang }} · {{ $b->type }}</div></td>
                            <td class="px-5 py-4 text-muted">{{ $b->ukuran ?: '-' }}</td>
                            <td class="px-5 py-4 text-muted">{{ $b->dibeli_oleh ?: '-' }}</td>
                            <td class="px-5 py-4 font-medium">{{ $b->jumlah_barang }}</td>
                            <td class="px-5 py-4 font-semibold">Rp {{ number_format($b->harga_satuan, 0, ',', '.') }}</td>
                            <td class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $b->status === 'tersedia' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">{{ ucfirst($b->status) }}</span></td>
                            <td class="px-5 py-4"><div class="flex justify-end"><button wire:click="edit({{ $b->id }})" class="rounded-lg px-3 py-2 text-xs font-semibold text-navy hover:bg-navy/10">Edit</button><button x-on:click="confirmDelete({{ $b->id }}, @js($b->nama_barang))" class="rounded-lg px-3 py-2 text-xs font-semibold text-danger hover:bg-danger/10">Hapus</button></div></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-14 text-center text-muted">Tidak ada barang yang sesuai dengan filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($barangs->hasPages())<div class="border-t border-border px-5 py-3">{{ $barangs->links('components.pagination') }}</div>@endif
    </div>

    <x-modal name="barang-form" :show="$form" maxWidth="4xl">
        <div class="border-b border-border px-6 py-5"><h3 class="font-display text-xl">{{ $barangId ? 'Edit Barang' : 'Tambah Barang' }}</h3><p class="mt-1 text-sm text-muted">Kelola informasi stok dan pembelian.</p></div>
        <div class="grid max-h-[65vh] gap-4 overflow-y-auto px-6 py-5 sm:grid-cols-2 md:grid-cols-3">
            <label class="text-sm font-semibold">Nama barang<input wire:model="nama_barang" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Nama barang">@error('nama_barang')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Ukuran<input wire:model="ukuran" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Ukuran"></label>
            <label class="text-sm font-semibold">Tipe<select wire:model="type" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@foreach($typeOptions->merge(['Jas Pria', 'Celana Pria', 'Jas Wanita', 'Celana Wanita', 'Rok Wanita', 'Dasi'])->unique()->sort()->values() as $typeOption)<option value="{{ $typeOption }}">{{ $typeOption }}</option>@endforeach</select></label>
            <label class="text-sm font-semibold">Jumlah<input type="number" wire:model="jumlah_barang" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@error('jumlah_barang')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Harga satuan<input type="number" wire:model="harga_satuan" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal">@error('harga_satuan')<span class="mt-1 block text-xs text-danger">{{ $message }}</span>@enderror</label>
            <label class="text-sm font-semibold">Status<select wire:model="status" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal"><option value="tersedia">Tersedia</option><option value="disewa">Disewa</option></select></label>
            <label class="text-sm font-semibold">Merk<input wire:model="merk" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Merk"></label>
            <label class="text-sm font-semibold">Toko<input wire:model="toko" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Toko"></label>
            <label class="text-sm font-semibold">Dibeli oleh<input wire:model="dibeli_oleh" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="Nama pembeli"></label>
            <label class="text-sm font-semibold">Tanggal beli<input type="date" wire:model="tanggal_beli" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal"></label>
            <label class="text-sm font-semibold sm:col-span-2">Link pembelian<input wire:model="link_pembelian" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal" placeholder="https://..."></label>
            <label class="text-sm font-semibold sm:col-span-2 md:col-span-3">Keterangan<textarea wire:model="keterangan" rows="3" class="mt-2 w-full rounded-xl px-3 py-2.5 text-sm font-normal"></textarea></label>
        </div>
        <div class="flex justify-end gap-2 border-t border-border bg-background/40 px-6 py-4"><button wire:click="$set('form', false)" class="rounded-xl border border-border px-4 py-2.5 text-sm font-semibold text-muted hover:bg-white">Batal</button><button wire:click="save" wire:loading.attr="disabled" class="rounded-xl bg-navy px-5 py-2.5 text-sm font-semibold text-white">Simpan Barang</button></div>
    </x-modal>
</div>
