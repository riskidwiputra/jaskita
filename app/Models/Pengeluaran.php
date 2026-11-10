<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Pengeluaran extends Model {
    use SoftDeletes;
    protected $fillable=['tanggal','kategori','jumlah','keterangan','dikeluarkan_oleh','deleted_by'];
    protected function casts(): array { return ['tanggal'=>'date','jumlah'=>'integer']; }
}
