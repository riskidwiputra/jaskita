<?php

namespace App\Livewire;

use App\Models\Penyewa;
use Livewire\Component;
use Livewire\WithPagination;

class PenyewaManager extends Component
{
    use WithPagination;

    public bool $form = false;

    public ?int $penyewaId = null;

    public $nama = '';

    public $no_hp = '';

    public $alamat = '';

    public $catatan = '';

    public $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate(['nama' => 'required']);
        $data = ['nama' => $this->nama, 'no_hp' => $this->no_hp, 'alamat' => $this->alamat, 'catatan' => $this->catatan];
        $editing = (bool) $this->penyewaId;
        $this->penyewaId ? Penyewa::findOrFail($this->penyewaId)->update($data) : Penyewa::create($data);
        $this->reset(['form', 'penyewaId', 'nama', 'no_hp', 'alamat', 'catatan']);
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: $editing ? 'Penyewa diperbarui' : 'Penyewa ditambahkan', message: 'Data penyewa berhasil disimpan.');
    }

    public function edit(int $id)
    {
        $b = Penyewa::findOrFail($id);
        $this->penyewaId = $b->id;
        foreach (['nama', 'no_hp', 'alamat', 'catatan'] as $k) {
            $this->$k = $b->$k;
        } $this->form = true;
    }

    public function delete(int $id)
    {
        $b = Penyewa::findOrFail($id);
        $b->deleted_by = auth()->user()->email;
        $b->save();
        $b->delete();
        $this->resetPage();
        $this->dispatch('notify', type: 'success', title: 'Penyewa dihapus', message: 'Data penyewa berhasil dihapus.');
    }

    public function render()
    {
        return view('livewire.penyewa-manager', ['penyewas' => Penyewa::where('nama', 'like', '%'.$this->search.'%')->latest()->paginate(10)]);
    }
}
