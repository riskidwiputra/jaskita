<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaksi_sewas', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->unsignedBigInteger('penyewa_id')->nullable();
            $table->text('nama_penyewa_manual')->nullable();
            $table->text('no_hp_manual')->nullable();
            $table->text('jenis_jaminan')->nullable();
            $table->longText('foto_peminjam')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('deleted_by')->nullable();
            $table->dateTime('tanggal_sewa', 3)->nullable();
            $table->dateTime('tanggal_kembali_rencana', 3)->nullable();
            $table->dateTime('tanggal_kembali_aktual', 3)->nullable();
            $table->string('status', 191)->nullable()->default('berjalan');
            $table->bigInteger('harga_sewa')->nullable();
            $table->index('deleted_at', 'idx_transaksi_sewas_deleted_at');
            $table->foreign('penyewa_id', 'fk_transaksi_sewas_penyewa')->references('id')->on('penyewas');
        });
    }

    public function down(): void { Schema::dropIfExists('transaksi_sewas'); }
};
