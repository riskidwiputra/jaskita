<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('review_photos', function (Blueprint $table) {
            $table->id();
            $table->timestamps(3);
            $table->softDeletes('deleted_at', 3);
            $table->unsignedBigInteger('review_id');
            $table->string('url', 500);
            $table->index('deleted_at', 'idx_review_photos_deleted_at');
            $table->index('review_id', 'idx_review_photos_review_id');
            $table->foreign('review_id', 'fk_reviews_foto')->references('id')->on('reviews');
        });
    }

    public function down(): void { Schema::dropIfExists('review_photos'); }
};
