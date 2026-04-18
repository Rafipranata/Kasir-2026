<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Meja;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    public function showMeja(string $id)
    {
        $meja = Meja::findOrFail($id);
        $kategoris = Kategori::with(['produks' => function($query) {
            $query->where('ketersediaan', true);
        }])->get();

        return view('order.meja', compact('meja', 'kategoris'));
    }

    public function checkout(Request $request)
    {
        $validated = $request->validate([
            'meja_id' => 'nullable|exists:mejas,id',
            'nama_pelanggan' => 'required|string|max:255',
            'tipe_order' => 'required|in:dine_in,take_away',
            'metode_pembayaran' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.produk_id' => 'required|exists:produks,id',
            'items.*.qty' => 'required|integer|min:1',
        ]);

        $order = $this->orderService->createOrder($validated);

        return response()->json([
            'success' => true,
            'redirect' => route('order.success', ['kode' => $order->kode_pesanan])
        ]);
    }

    public function success(string $kode)
    {
        $order = Order::with(['orderItems.produk', 'meja'])
            ->where('kode_pesanan', $kode)
            ->firstOrFail();

        return view('order.success', compact('order'));
    }
}
