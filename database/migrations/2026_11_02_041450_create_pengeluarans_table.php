<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengeluarans', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->dateTime('tanggal', 3)->nullable();
            $table->text('kategori')->nullable();
            $table->bigInteger('jumlah')->nullable();
            $table->text('keterangan')->nullable();
            $table->text('dikeluarkan_oleh')->nullable();
            $table->text('deleted_by')->nullable();
            $table->index('deleted_at', 'idx_pengeluarans_deleted_at');
        });
    }

    public function down(): void { Schema::dropIfExists('pengeluarans'); }
};
