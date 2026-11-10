<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tokens', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->unsignedBigInteger('transaksi_sewa_id');
            $table->string('token_hash', 64);
            $table->dateTime('expires_at', 3);
            $table->dateTime('used_at', 3)->nullable();
            $table->unique('transaksi_sewa_id', 'idx_tokens_transaksi_sewa_id');
            $table->unique('token_hash', 'idx_tokens_token_hash');
            $table->index('deleted_at', 'idx_tokens_deleted_at');
        });
    }

    public function down(): void { Schema::dropIfExists('tokens'); }
};
