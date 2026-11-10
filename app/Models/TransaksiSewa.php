<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TransaksiSewa extends Model {
    use SoftDeletes;
    protected $fillable=['penyewa_id','nama_penyewa_manual','no_hp_manual','jenis_jaminan','foto_peminjam','keterangan','tanggal_sewa','tanggal_kembali_rencana','tanggal_kembali_aktual','status','harga_sewa','deleted_by'];
    protected function casts(): array { return ['tanggal_sewa'=>'date','tanggal_kembali_rencana'=>'date','tanggal_kembali_aktual'=>'date','harga_sewa'=>'integer']; }
    public function penyewa() { return $this->belongsTo(Penyewa::class); }
    public function barang() { return $this->hasMany(TransaksiSewaBarang::class); }
    public function dendas() { return $this->hasMany(Denda::class); }
    public function review() { return $this->hasOne(Review::class); }
    public function reviewToken() { return $this->hasOne(ReviewToken::class); }
    public function namaPenyewa(): string { return $this->penyewa?->nama ?: $this->nama_penyewa_manual ?: '-'; }
}
