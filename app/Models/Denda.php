<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Denda extends Model {
    use SoftDeletes;
    protected $fillable=['transaksi_sewa_id','jumlah','alasan','deleted_by'];
    public function transaksi() { return $this->belongsTo(TransaksiSewa::class,'transaksi_sewa_id'); }
}
