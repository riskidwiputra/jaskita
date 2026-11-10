<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barangs', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->text('kode_barang')->nullable();
            $table->text('nama_barang')->nullable();
            $table->text('ukuran')->nullable();
            $table->bigInteger('jumlah_barang')->nullable();
            $table->bigInteger('harga_satuan')->nullable();
            $table->text('type')->nullable();
            $table->string('status', 191)->nullable()->default('tersedia');
            $table->text('dibeli_oleh')->nullable();
            $table->dateTime('tanggal_beli', 3)->nullable();
            $table->text('merk')->nullable();
            $table->text('toko')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('link_pembelian')->nullable();
            $table->text('deleted_by')->nullable();
            $table->index('deleted_at', 'idx_barangs_deleted_at');
        });
    }

    public function down(): void { Schema::dropIfExists('barangs'); }
};
