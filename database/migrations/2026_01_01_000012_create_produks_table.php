<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('produks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->constrained('kategoris')->cascadeOnDelete();
            $table->string('nama_produk', 255);
            $table->decimal('harga_produk', 12, 2)->default(0);
            $table->boolean('ketersediaan')->default(true);
            $table->timestamps();

            $table->index('kategori_id');
            $table->index('ketersediaan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('produks');
    }
};
