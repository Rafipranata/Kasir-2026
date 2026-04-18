<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produk extends Model
{
    protected $table = 'produks';

    protected $fillable = [
        'kategori_id',
        'nama_produk',
        'gambar_produk',
        'harga_produk',
        'ketersediaan',
    ];

    protected $casts = [
        'harga_produk' => 'decimal:2',
        'ketersediaan' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function orders(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_items', 'produk_id', 'order_id')
            ->withPivot(['qty', 'harga', 'subtotal'])
            ->withTimestamps();
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'produk_id');
    }

    public function getHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->harga_produk, 0, ',', '.');
    }
}
