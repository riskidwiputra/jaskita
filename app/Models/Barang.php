<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Barang extends Model {
    use SoftDeletes;
    protected $fillable=['kode_barang','nama_barang','ukuran','jumlah_barang','harga_satuan','type','status','dibeli_oleh','tanggal_beli','merk','toko','keterangan','link_pembelian','deleted_by'];
    protected function casts(): array { return ['tanggal_beli'=>'date','harga_satuan'=>'integer','jumlah_barang'=>'integer']; }
}
