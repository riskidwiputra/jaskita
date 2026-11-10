<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Penyewa extends Model {
    use SoftDeletes;
    protected $fillable=['nama','no_hp','alamat','catatan','deleted_by'];
    public function transaksi() { return $this->hasMany(TransaksiSewa::class); }
}
