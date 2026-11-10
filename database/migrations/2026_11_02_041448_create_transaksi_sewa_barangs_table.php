<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_sewa_barangs', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->unsignedBigInteger('transaksi_sewa_id')->nullable();
            $table->unsignedBigInteger('barang_id')->nullable();
            $table->bigInteger('harga_item')->nullable();
            $table->index('deleted_at', 'idx_transaksi_sewa_barangs_deleted_at');
            $table->foreign('barang_id', 'fk_transaksi_sewa_barangs_barang')->references('id')->on('barangs');
            $table->foreign('transaksi_sewa_id', 'fk_transaksi_sewas_barang')->references('id')->on('transaksi_sewas');
        });
    }

    public function down(): void { Schema::dropIfExists('transaksi_sewa_barangs'); }
};
