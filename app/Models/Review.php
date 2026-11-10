<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Review extends Model {
    use SoftDeletes;
    protected $fillable=['transaksi_sewa_id','nama_penyewa','item_sewa','tanggal_sewa','tanggal_kembali','rating','kritik_saran'];
    protected function casts(): array { return ['tanggal_sewa'=>'date','tanggal_kembali'=>'date','rating'=>'integer']; }
    public function transaksi() { return $this->belongsTo(TransaksiSewa::class,'transaksi_sewa_id'); }
    public function foto() { return $this->hasMany(ReviewPhoto::class); }
}
