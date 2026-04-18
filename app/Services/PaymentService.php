<?php

namespace App\Services;

use App\Models\Order;

class PaymentService
{
    /**
     * Process payment for an order
     */
    public function processPayment(Order $order, string $metode): bool
    {
        // Bisa diextend untuk payment gateway
        $order->update([
            'metode_pembayaran' => $metode,
            'status'            => Order::STATUS_PAID,
        ]);

        return true;
    }

    /**
     * Get available payment methods
     */
    public function getMetodePembayaran(): array
    {
        return [
            'cash'     => 'Tunai',
            'qris'     => 'QRIS',
            'transfer' => 'Transfer Bank',
            'debit'    => 'Kartu Debit',
        ];
    }
}
