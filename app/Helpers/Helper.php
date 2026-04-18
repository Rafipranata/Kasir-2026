<?php

namespace App\Helpers;

class Helper
{
    public static function rupiahFormat(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    public static function generateKode(string $prefix = 'ORD', int $length = 6): string
    {
        return $prefix . '-' . strtoupper(substr(md5(uniqid()), 0, $length));
    }

    public static function statusBadgeColor(string $status): string
    {
        return match ($status) {
            'pending'   => 'warning',
            'accepted'  => 'info',
            'paid'      => 'success',
            'completed' => 'gray',
            default     => 'secondary',
        };
    }
}
