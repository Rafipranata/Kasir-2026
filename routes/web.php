<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\ReceiptController;

Route::get('/', function () {
    return redirect('/admin');
});

Route::prefix('order')->group(function () {
    Route::get('meja/{id}', [OrderController::class, 'showMeja'])->name('order.meja');
    Route::post('checkout', [OrderController::class, 'checkout'])->name('order.checkout');
    Route::get('success/{kode}', [OrderController::class, 'success'])->name('order.success');
});

Route::middleware(['auth', 'role:admin,kasir'])->prefix('kasir')->group(function () {
    Route::get('/', [KasirController::class, 'index'])->name('kasir.index');
    Route::post('cari', [KasirController::class, 'cariOrder'])->name('kasir.cari');
    Route::post('accept/{order}', [KasirController::class, 'acceptOrder'])->name('kasir.accept');
    Route::post('bayar/{order}', [KasirController::class, 'bayar'])->name('kasir.bayar');
    Route::post('selesaikan/{order}', [KasirController::class, 'selesaikan'])->name('kasir.selesaikan');
});

// ── Receipt / Struk PDF ──────────────────────────────────────────────────────
Route::middleware(['auth'])->prefix('receipt')->name('receipt.')->group(function () {
    // Tampilkan PDF inline di browser (untuk iframe preview)
    Route::get('{order}/stream', [ReceiptController::class, 'stream'])->name('stream');
    // Download PDF struk
    Route::get('{order}/download', [ReceiptController::class, 'download'])->name('download');
});
