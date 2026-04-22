@extends('layout.app')

@section('title', 'Pesanan Sukses - ' . $order->kode_pesanan)

@section('content')
<div class="min-h-screen bg-gray-50/50 flex flex-col items-center justify-center p-6">
    <div class="w-full max-w-sm mt-8 mb-8">
        <!-- Main Card -->
        <div class="bg-white rounded-3xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] p-8 w-full text-center mb-6 border border-gray-100/80">
            
            <!-- Animated Check Icon -->
            <div class="mx-auto w-20 h-20 bg-green-50 rounded-full flex items-center justify-center mb-6 relative">
                <div class="absolute inset-0 bg-green-100 rounded-full animate-ping opacity-30"></div>
                <svg class="h-10 w-10 text-green-500 relative z-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Berhasil!</h2>
            <p class="text-gray-500 text-sm mb-8">Terima kasih, <span class="text-gray-800 font-semibold">{{ $order->nama_pelanggan ?? 'Tamu' }}</span></p>
            
            <!-- Kode Pesanan Section -->
            <div class="bg-gray-50 rounded-2xl p-6 mb-8 border border-gray-100">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Kode Pesanan</p>
                <p class="text-3xl font-black text-gray-900 tracking-widest mb-3">{{ $order->kode_pesanan }}</p>
                
                @if($order->meja)
                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-white border border-gray-200 rounded-full shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                        <span class="text-[10px] font-bold text-gray-600 uppercase tracking-wide">Meja {{ $order->meja->nomor_meja }}</span>
                    </div>
                @endif
            </div>

            <!-- Receipt/Summary -->
            <div class="text-left">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-4 border-b border-gray-100 pb-2">
                    Ringkasan
                </h4>
                
                <div class="space-y-3 mb-5 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($order->orderItems as $item)
                        <div class="flex justify-between items-start text-sm">
                            <div class="flex gap-2">
                                <span class="font-semibold text-gray-900">{{ $item->qty }}x</span>
                                <span class="text-gray-600">{{ $item->produk->nama_produk }}</span>
                            </div>
                            <span class="text-gray-900 font-medium whitespace-nowrap ml-4">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
                
                <div class="border-t border-gray-100 pt-4 mb-4">
                    <div class="flex justify-between items-center mb-2 text-sm">
                        <span class="text-gray-500">Metode</span>
                        <span class="text-gray-800 font-semibold uppercase">{{ $order->metode_pembayaran }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 text-sm">
                        <span class="text-gray-500">Tipe</span>
                        <span class="text-gray-800 font-semibold">{{ $order->tipe_order_label }}</span>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                        <span class="text-sm font-semibold text-gray-500">Total</span>
                        <span class="text-xl font-bold text-gray-900">{{ $order->total_harga_formatted }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Instructions -->
            <div class="mt-6 text-amber-800 text-xs font-medium p-4 bg-amber-50 rounded-xl border border-amber-200/60 text-center flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span>Tunjukkan <strong>Kode Pesanan</strong> ke kasir untuk proses pembayaran.</span>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex justify-center">
            @if($order->meja)
                <a href="{{ route('order.meja', $order->meja->id) }}" class="inline-flex items-center justify-center gap-2 text-gray-500 hover:text-gray-900 font-medium text-sm transition-colors py-2 px-4 rounded-full hover:bg-gray-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                    Pesan Menu Lainnya
                </a>
            @endif
        </div>
    </div>
</div>

<style>
/* Custom Scrollbar for list */
.custom-scrollbar::-webkit-scrollbar {
  width: 4px;
}
.custom-scrollbar::-webkit-scrollbar-track {
  background: transparent; 
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
