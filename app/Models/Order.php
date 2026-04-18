<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'meja_id',
        'nama_pelanggan',
        'tipe_order',
        'status',
        'metode_pembayaran',
        'total_harga',
        'kode_pesanan',
    ];

    protected $casts = [
        'total_harga' => 'decimal:2',
    ];

    // Status constants
    const STATUS_PENDING   = 'pending';
    const STATUS_ACCEPTED  = 'accepted';
    const STATUS_PAID      = 'paid';
    const STATUS_COMPLETED = 'completed';

    // Tipe order constants
    const TIPE_DINE_IN   = 'dine_in';
    const TIPE_TAKE_AWAY = 'take_away';

    public function meja(): BelongsTo
    {
        return $this->belongsTo(Meja::class, 'meja_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function produks(): BelongsToMany
    {
        return $this->belongsToMany(Produk::class, 'order_items', 'order_id', 'produk_id')
            ->withPivot(['qty', 'harga', 'subtotal'])
            ->withTimestamps();
    }

    public function getTotalHargaFormattedAttribute(): string
    {
        return 'Rp ' . number_format($this->total_harga, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'Menunggu',
            'accepted'  => 'Diterima',
            'paid'      => 'Dibayar',
            'completed' => 'Selesai',
            default     => ucfirst($this->status),
        };
    }

    public function getTipeOrderLabelAttribute(): string
    {
        return match ($this->tipe_order) {
            'dine_in'   => 'Makan di Tempat',
            'take_away' => 'Bawa Pulang',
            default     => $this->tipe_order,
        };
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    public function scopePaid($query)
    {
        return $query->where('status', self::STATUS_PAID);
    }
}
