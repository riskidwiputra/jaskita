<?php

namespace App\Livewire;

use App\Models\Pengeluaran;
use Livewire\Component;
use Livewire\WithPagination;

class PengeluaranManager extends Component
{
    use WithPagination;

    public bool $form = false;

    public ?int $pengeluaranId = null;

    public $tanggal = '';

    public $kategori = 'Operasional';

    public $jumlah = 0;

    public $keterangan = '';

    public $dikeluarkan_oleh = '';

    public string $tanggalMulai = '';

    public string $tanggalSelesai = '';

    public string $filterDikeluarkanOleh = '';

    public function updated($property): void
    {
        if (in_array($property, ['tanggalMulai', 'tanggalSelesai', 'filterDikeluarkanOleh'], true)) {
            $this->resetPage();
        }
    }

    public function save(): void
    {
        $this->validate([
            'tanggal' => 'required|date',
            'kategori' => 'required|string|max:100',
            'jumlah' => 'required|integer|min:0',
        ]);

        $user = auth()->user();
        $data = [
            'tanggal' => $this->tanggal,
            'kategori' => $this->kategori,
            'jumlah' => $this->jumlah,
            'keterangan' => $this->keterangan,
            'dikeluarkan_oleh' => $this->dikeluarkan_oleh ?: ($user->nama ?: $user->email),
        ];
        $editing = (bool) $this->pengeluaranId;

        if ($this->pengeluaranId) {
            Pengeluaran::findOrFail($this->pengeluaranId)->update($data);
        } else {
            Pengeluaran::create($data);
        }

        $this->reset(['form', 'pengeluaranId', 'tanggal', 'kategori', 'jumlah', 'keterangan', 'dikeluarkan_oleh']);
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: $editing ? 'Pengeluaran diperbarui' : 'Pengeluaran ditambahkan', message: 'Data pengeluaran berhasil disimpan.');
    }

    public function edit(int $id): void
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $this->pengeluaranId = $pengeluaran->id;
        $this->tanggal = $pengeluaran->tanggal->toDateString();
        $this->kategori = $pengeluaran->kategori;
        $this->jumlah = $pengeluaran->jumlah;
        $this->keterangan = $pengeluaran->keterangan;
        $this->dikeluarkan_oleh = $pengeluaran->dikeluarkan_oleh;
        $this->form = true;
    }

    public function delete(int $id): void
    {
        $pengeluaran = Pengeluaran::findOrFail($id);
        $pengeluaran->deleted_by = auth()->user()->email;
        $pengeluaran->save();
        $pengeluaran->delete();
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Pengeluaran dihapus', message: 'Data pengeluaran berhasil dihapus.');
    }

    public function render()
    {
        $query = Pengeluaran::query()
            ->when($this->tanggalMulai !== '', fn ($builder) => $builder->whereDate('tanggal', '>=', $this->tanggalMulai))
            ->when($this->tanggalSelesai !== '', fn ($builder) => $builder->whereDate('tanggal', '<=', $this->tanggalSelesai))
            ->when($this->filterDikeluarkanOleh !== '', fn ($builder) => $builder->where('dikeluarkan_oleh', $this->filterDikeluarkanOleh));

        return view('livewire.pengeluaran-manager', [
            'pengeluarans' => $query->latest()->paginate(10),
            'pembuatOptions' => Pengeluaran::query()
                ->whereNotNull('dikeluarkan_oleh')
                ->where('dikeluarkan_oleh', '!=', '')
                ->distinct()
                ->orderBy('dikeluarkan_oleh')
                ->pluck('dikeluarkan_oleh'),
        ]);
    }
}
