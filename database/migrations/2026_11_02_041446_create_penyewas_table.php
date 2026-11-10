<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penyewas', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->text('nama')->nullable();
            $table->text('no_hp')->nullable();
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->index('deleted_at', 'idx_penyewas_deleted_at');
        });
    }

    public function down(): void { Schema::dropIfExists('penyewas'); }
};
