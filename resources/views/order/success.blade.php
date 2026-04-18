@extends('layout.app')

@section('title', 'Pesanan Sukses - ' . $order->kode_pesanan)

@section('content')
<div class="min-h-screen bg-gray-50 flex flex-col items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl p-8 max-w-sm w-full text-center mb-6 border border-gray-100 relative overflow-hidden">
        <!-- Decorative Header -->
        <div class="bg-blue-600 h-2 absolute top-0 left-0 right-0"></div>
        
        <div class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6 shadow-sm shadow-green-200">
            <svg class="h-10 w-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
        </div>
        
        <h2 class="text-2xl font-bold text-gray-800 mb-2">Pesanan Berhasil!</h2>
        <p class="text-gray-500 text-sm mb-6">Terima kasih telah memesan, {{ $order->nama_pelanggan ?? 'Tamu' }}.</p>
        
        <div class="bg-gray-50 rounded-2xl p-5 mb-6 border border-gray-200 border-dashed">
            <p class="text-xs text-gray-500 uppercase tracking-wider font-semibold mb-1">Kode Pesanan</p>
            <p class="text-3xl font-extrabold text-blue-600 tracking-widest">{{ $order->kode_pesanan }}</p>
            @if($order->meja)
                <p class="text-sm font-medium mt-2 bg-blue-100 text-blue-800 inline-block px-3 py-1 rounded-full">Meja #{{ $order->meja->nomor_meja }}</p>
            @endif
        </div>

        <div class="text-left mb-6">
            <p class="text-sm font-medium text-gray-500 mb-2">Ringkasan Pesanan:</p>
            <ul class="space-y-2 mb-4 max-h-40 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($order->orderItems as $item)
                    <li class="flex justify-between text-sm">
                        <span class="text-gray-700"><span class="font-bold text-gray-900 mr-1">{{ $item->qty }}x</span> {{ $item->produk->nama_produk }}</span>
                        <span class="text-gray-600 font-medium whitespace-nowrap ml-2">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <div class="border-t border-gray-200 pt-3 flex justify-between font-bold">
                <span class="text-gray-800">Total Pembayaran</span>
                <span class="text-blue-600 text-lg">{{ $order->total_harga_formatted }}</span>
            </div>
            <div class="flex justify-between mt-1 text-sm font-medium">
                <span class="text-gray-500">Metode</span>
                <span class="text-gray-800">{{ strtoupper($order->metode_pembayaran) }}</span>
            </div>
            <div class="flex justify-between mt-1 text-sm font-medium">
                <span class="text-gray-500">Tipe Order</span>
                <span class="text-gray-800">{{ $order->tipe_order_label }}</span>
            </div>
        </div>

        <div class="bg-blue-50 text-blue-800 text-sm p-4 rounded-xl mb-6">
            Sampaikan <strong>Kode Pesanan</strong> ini kepada kasir untuk memproses pembayaran Anda.
        </div>
        
    </div>
    
    @if($order->meja)
        <a href="{{ route('order.meja', $order->meja->id) }}" class="text-blue-600 font-medium text-sm hover:underline flex items-center gap-1 bg-white px-5 py-2.5 rounded-full shadow-sm">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            Kembali ke Menu
        </a>
    @endif
</div>

<style>
/* Custom Scrollbar for list */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
  background: #cbd5e1; 
  border-radius: 10px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: #94a3b8; 
}
</style>
@endsection
