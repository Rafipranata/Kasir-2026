<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Generate unique kode pesanan
     */
    public function generateKodePesanan(): string
    {
        do {
            $kode = 'ORD-' . strtoupper(Str::random(6));
        } while (Order::where('kode_pesanan', $kode)->exists());

        return $kode;
    }

    /**
     * Hitung total dari array items
     */
    public function hitungTotal(array $items): float
    {
        $total = 0;
        foreach ($items as $item) {
            $produk = Produk::find($item['produk_id']);
            if ($produk) {
                $total += $produk->harga_produk * $item['qty'];
            }
        }
        return $total;
    }

    /**
     * Buat order baru dengan transaction
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            $items   = $data['items'] ?? [];
            $total   = $this->hitungTotal($items);
            $kode    = $this->generateKodePesanan();

            $order = Order::create([
                'meja_id'          => $data['meja_id'] ?? null,
                'nama_pelanggan'   => $data['nama_pelanggan'] ?? null,
                'tipe_order'       => $data['tipe_order'],
                'status'           => Order::STATUS_PENDING,
                'metode_pembayaran'=> $data['metode_pembayaran'],
                'total_harga'      => $total,
                'kode_pesanan'     => $kode,
            ]);

            foreach ($items as $item) {
                $produk = Produk::findOrFail($item['produk_id']);
                $subtotal = $produk->harga_produk * $item['qty'];

                OrderItem::create([
                    'order_id'   => $order->id,
                    'produk_id'  => $produk->id,
                    'qty'        => $item['qty'],
                    'harga'      => $produk->harga_produk,
                    'subtotal'   => $subtotal,
                ]);
            }

            return $order->fresh(['orderItems.produk', 'meja']);
        });
    }

    /**
     * Accept order
     */
    public function acceptOrder(Order $order): Order
    {
        $order->update(['status' => Order::STATUS_ACCEPTED]);
        return $order->fresh();
    }

    /**
     * Mark order as paid
     */
    public function markAsPaid(Order $order): Order
    {
        $order->update(['status' => Order::STATUS_PAID]);
        return $order->fresh();
    }

    /**
     * Mark order as completed
     */
    public function completeOrder(Order $order): Order
    {
        $order->update(['status' => Order::STATUS_COMPLETED]);
        return $order->fresh();
    }
}
