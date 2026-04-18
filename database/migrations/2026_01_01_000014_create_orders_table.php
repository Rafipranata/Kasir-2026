<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('meja_id')->nullable()->constrained('mejas')->nullOnDelete();
            $table->string('nama_pelanggan', 100)->nullable();
            $table->enum('tipe_order', ['dine_in', 'take_away'])->default('dine_in');
            $table->enum('status', ['pending', 'accepted', 'paid', 'completed'])->default('pending');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->string('kode_pesanan', 20)->unique();
            $table->timestamps();

            $table->index('status');
            $table->index('tipe_order');
            $table->index('meja_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
