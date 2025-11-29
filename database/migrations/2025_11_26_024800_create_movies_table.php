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
        Schema::create('movies', function (Blueprint $table) {
            $table->uuid('id')->unique();
            $table->string('title');
            $table->string('slug');
            $table->string('production_house');
            $table->json('genre');
            $table->string('covers')->nullable();
            $table->integer('year')->nullable();
            $table->string('qr_code')->nullable();
            $table->integer('stock')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
