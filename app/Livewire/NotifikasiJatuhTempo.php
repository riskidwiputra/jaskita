<?php
namespace App\Livewire;
use App\Models\TransaksiSewa; use Livewire\Component;
class NotifikasiJatuhTempo extends Component { public function render(){ return view('livewire.notifikasi-jatuh-tempo',['transaksis'=>TransaksiSewa::with(['penyewa','barang.barang'])->where('status','berjalan')->whereDate('tanggal_kembali_rencana','<=',now())->latest('tanggal_kembali_rencana')->get(),'jumlah'=>TransaksiSewa::where('status','berjalan')->whereDate('tanggal_kembali_rencana','<=',now())->count()]); } }
