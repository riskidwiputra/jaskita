<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->unsignedBigInteger('transaksi_sewa_id')->nullable();
            $table->bigInteger('jumlah')->nullable();
            $table->text('alasan')->nullable();
            $table->text('deleted_by')->nullable();
            $table->index('deleted_at', 'idx_dendas_deleted_at');
            $table->foreign('transaksi_sewa_id', 'fk_dendas_transaksi_sewa')->references('id')->on('transaksi_sewas');
        });
    }

    public function down(): void { Schema::dropIfExists('dendas'); }
};
