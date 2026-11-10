<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->text('nama')->nullable();
            $table->string('email', 191)->nullable()->unique('uni_users_email');
            $table->text('password_hash')->nullable();
            $table->string('role', 191)->nullable()->default('owner');
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->index('deleted_at', 'idx_users_deleted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
