<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransaksiSewaBarang extends Model {
    use SoftDeletes;
    protected $fillable=['transaksi_sewa_id','barang_id','harga_item'];
    public function transaksi() { return $this->belongsTo(TransaksiSewa::class,'transaksi_sewa_id'); }
    public function barang() { return $this->belongsTo(Barang::class); }
}
