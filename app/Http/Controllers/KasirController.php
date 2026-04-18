<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    protected OrderService $orderService;
    protected PaymentService $paymentService;

    public function __construct(OrderService $orderService, PaymentService $paymentService)
    {
        $this->orderService = $orderService;
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        return view('kasir.index');
    }

    public function cariOrder(Request $request)
    {
        $request->validate([
            'kode_pesanan' => 'required|string',
        ]);

        $order = Order::with(['orderItems.produk', 'meja'])
            ->where('kode_pesanan', $request->kode_pesanan)
            ->first();

        if (!$order) {
            return back()->with('error', 'Pesanan tidak ditemukan.');
        }

        return view('kasir.index', compact('order'));
    }

    public function acceptOrder(Request $request, Order $order)
    {
        $this->orderService->acceptOrder($order);
        return redirect()->route('kasir.index', ['kode_pesanan' => $order->kode_pesanan])->with('success', 'Pesanan diterima.');
    }

    public function bayar(Request $request, Order $order)
    {
        $request->validate([
            'metode_pembayaran' => 'required|string'
        ]);

        $this->paymentService->processPayment($order, $request->metode_pembayaran);
        
        return redirect()->route('kasir.index', ['kode_pesanan' => $order->kode_pesanan])->with('success', 'Pembayaran berhasil dikonfirmasi.');
    }
    
    public function selesaikan(Request $request, Order $order)
    {
        $this->orderService->completeOrder($order);
        return redirect()->route('kasir.index', ['kode_pesanan' => $order->kode_pesanan])->with('success', 'Pesanan selesai.');
    }
}
