<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('produk_id')->constrained('produks')->restrictOnDelete();
            $table->unsignedInteger('qty')->default(1);
            $table->decimal('harga', 12, 2)->default(0);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->timestamps();

            $table->index('order_id');
            $table->index('produk_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
