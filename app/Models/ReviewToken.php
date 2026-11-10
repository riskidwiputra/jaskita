<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class ReviewToken extends Model {
    protected $table = 'tokens';
    use SoftDeletes;
    protected $fillable=['transaksi_sewa_id','token_hash','expires_at','used_at'];
    protected function casts(): array { return ['expires_at'=>'datetime','used_at'=>'datetime']; }
}
