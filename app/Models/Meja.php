<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Meja extends Model
{
    protected $table = 'mejas';

    protected $fillable = [
        'nomor_meja',
        'qr_code',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'meja_id');
    }

    /**
     * Generate and store QR code for this meja.
     */
    public function generateQrCode(): void
    {
        $url = url('/order/meja/' . $this->id);
        $svg = QrCode::format('svg')->size(200)->generate($url);
        $this->qr_code = base64_encode($svg);
        $this->save();
    }

    public function getQrCodeUrlAttribute(): string
    {
        return url('/order/meja/' . $this->id);
    }
}
