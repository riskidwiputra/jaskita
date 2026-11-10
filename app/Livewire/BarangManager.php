<?php

namespace App\Livewire;

use App\Models\Barang;
use Livewire\Component;
use Livewire\WithPagination;

class BarangManager extends Component
{
    use WithPagination;

    public bool $form = false;

    public ?int $barangId = null;

    public $nama_barang = '';

    public $ukuran = '';

    public $jumlah_barang = 1;

    public $harga_satuan = 0;

    public $type = 'Jas Pria';

    public $status = 'tersedia';

    public $dibeli_oleh = '';

    public $tanggal_beli = '';

    public $merk = '';

    public $toko = '';

    public $keterangan = '';

    public $link_pembelian = '';

    public $search = '';

    public string $filterUkuran = '';

    public string $filterType = '';

    public string $filterPembeli = '';

    public function updated($property): void
    {
        if (in_array($property, ['search', 'filterUkuran', 'filterType', 'filterPembeli'], true)) {
            $this->resetPage();
        }
    }

    public function clearFilters(): void
    {
        $this->reset(['search', 'filterUkuran', 'filterType', 'filterPembeli']);
        $this->resetPage();
    }

    public function save(): void
    {
        $this->validate([
            'nama_barang' => 'required|string|max:255',
            'ukuran' => 'nullable|string|max:50',
            'jumlah_barang' => 'required|integer|min:1',
            'harga_satuan' => 'required|integer|min:0',
            'type' => 'required|string|max:100',
        ]);

        $editing = (bool) $this->barangId;

        if ($this->barangId) {
            Barang::findOrFail($this->barangId)->update($this->formData());
        } else {
            $prefix = [
                'Jas Pria' => 'JP',
                'Celana Pria' => 'CP',
                'Jas Wanita' => 'JW',
                'Celana Wanita' => 'CW',
                'Rok Wanita' => 'RW',
                'Dasi' => 'DS',
            ][$this->type] ?? 'BR';
            $lastCode = Barang::withTrashed()->where('kode_barang', 'like', $prefix.'%')->orderByDesc('id')->value('kode_barang');
            preg_match('/(\d+)$/', (string) $lastCode, $matches);
            $number = isset($matches[1]) ? (int) $matches[1] + 1 : 1;

            Barang::create(array_merge($this->formData(), [
                'kode_barang' => $prefix.str_pad((string) $number, 3, '0', STR_PAD_LEFT),
                'status' => 'tersedia',
            ]));
        }

        $this->resetForm();
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: $editing ? 'Barang diperbarui' : 'Barang ditambahkan', message: 'Data barang berhasil disimpan.');
    }

    private function formData(): array
    {
        return [
            'nama_barang' => $this->nama_barang,
            'ukuran' => $this->ukuran,
            'jumlah_barang' => $this->jumlah_barang,
            'harga_satuan' => $this->harga_satuan,
            'type' => $this->type,
            'status' => $this->status,
            'dibeli_oleh' => $this->dibeli_oleh,
            'tanggal_beli' => $this->tanggal_beli ?: null,
            'merk' => $this->merk,
            'toko' => $this->toko,
            'keterangan' => $this->keterangan,
            'link_pembelian' => $this->link_pembelian,
        ];
    }

    public function edit(int $id): void
    {
        $barang = Barang::findOrFail($id);
        $this->barangId = $barang->id;

        foreach (['nama_barang', 'ukuran', 'jumlah_barang', 'harga_satuan', 'type', 'status', 'dibeli_oleh', 'tanggal_beli', 'merk', 'toko', 'keterangan', 'link_pembelian'] as $key) {
            $this->$key = $barang->$key;
        }

        $this->form = true;
    }

    public function delete(int $id): void
    {
        $barang = Barang::findOrFail($id);
        $barang->deleted_by = auth()->user()->email;
        $barang->save();
        $barang->delete();
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Barang dihapus', message: 'Data barang berhasil dihapus.');
    }

    private function resetForm(): void
    {
        $this->reset(['barangId', 'nama_barang', 'ukuran', 'jumlah_barang', 'harga_satuan', 'type', 'status', 'dibeli_oleh', 'tanggal_beli', 'merk', 'toko', 'keterangan', 'link_pembelian', 'form']);
    }

    public function render()
    {
        $barangs = Barang::query()
            ->when($this->search !== '', function ($query) {
                $query->where(function ($searchQuery) {
                    $searchQuery->where('nama_barang', 'like', '%'.$this->search.'%')
                        ->orWhere('kode_barang', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->filterUkuran !== '', fn ($query) => $query->where('ukuran', $this->filterUkuran))
            ->when($this->filterType !== '', fn ($query) => $query->where('type', $this->filterType))
            ->when($this->filterPembeli !== '', fn ($query) => $query->where('dibeli_oleh', $this->filterPembeli))
            ->latest()
            ->paginate(10);

        return view('livewire.barang-manager', [
            'barangs' => $barangs,
            'ukuranOptions' => Barang::query()->whereNotNull('ukuran')->where('ukuran', '!=', '')->distinct()->orderBy('ukuran')->pluck('ukuran'),
            'typeOptions' => Barang::query()->whereNotNull('type')->where('type', '!=', '')->distinct()->orderBy('type')->pluck('type'),
            'pembeliOptions' => Barang::query()->whereNotNull('dibeli_oleh')->where('dibeli_oleh', '!=', '')->distinct()->orderBy('dibeli_oleh')->pluck('dibeli_oleh'),
        ]);
    }
}
