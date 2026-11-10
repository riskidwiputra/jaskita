<?php

namespace App\Livewire;

use App\Models\Denda;
use App\Models\TransaksiSewa;
use Livewire\Component;
use Livewire\WithPagination;

class DendaManager extends Component
{
    use WithPagination;

    public bool $form = false;

    public ?int $dendaId = null;

    public $transaksi_sewa_id = '';

    public $jumlah = 0;

    public $alasan = '';

    public function save()
    {
        $this->validate(['transaksi_sewa_id' => 'required|exists:transaksi_sewas,id', 'jumlah' => 'required|integer|min:0']);
        $data = ['transaksi_sewa_id' => $this->transaksi_sewa_id, 'jumlah' => $this->jumlah, 'alasan' => $this->alasan];
        $editing = (bool) $this->dendaId;
        $this->dendaId ? Denda::findOrFail($this->dendaId)->update($data) : Denda::create($data);
        $this->reset(['form', 'dendaId', 'transaksi_sewa_id', 'jumlah', 'alasan']);
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: $editing ? 'Denda diperbarui' : 'Denda ditambahkan', message: 'Data denda berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $b = Denda::findOrFail($id);
        $this->dendaId = $b->id;
        foreach (['transaksi_sewa_id', 'jumlah', 'alasan'] as $k) {
            $this->$k = $b->$k;
        } $this->form = true;
    }

    public function delete(int $id)
    {
        $b = Denda::findOrFail($id);
        $b->deleted_by = auth()->user()->email;
        $b->save();
        $b->delete();
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Denda dihapus', message: 'Data denda berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.denda-manager', ['dendas' => Denda::with('transaksi')->latest()->paginate(10), 'transaksis' => TransaksiSewa::latest()->get(), 'totalJumlah' => Denda::sum('jumlah'), 'totalDenda' => Denda::count()]);
    }
}
