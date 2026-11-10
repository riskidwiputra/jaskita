<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('google_business_connections', function (Blueprint $table) {
            $table->id();
            $table->text('refresh_token');
            $table->string('location_name')->nullable();
            $table->string('location_title')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('google_business_connections');
    }
};
