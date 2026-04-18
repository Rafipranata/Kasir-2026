@extends('layout.app')

@section('title', 'Panel Kasir')

@section('content')
<div class="min-h-screen bg-gray-50 max-w-4xl mx-auto w-full md:shadow-xl md:border-x border-gray-100 flex flex-col" style="max-width: none;">
    <!-- Top Nav -->
    <nav class="bg-indigo-600 text-white p-4 shadow-md flex justify-between items-center sticky top-0 z-50">
        <h1 class="text-xl font-bold flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            Kasir POS
        </h1>
        <div class="flex items-center gap-4 text-sm font-medium text-indigo-100">
            <span>{{ auth()->user()->name ?? 'Kasir' }}</span>
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="hover:text-white bg-indigo-700 hover:bg-indigo-800 px-3 py-1.5 rounded-lg transition-colors">Logout</button>
            </form>
        </div>
    </nav>

    <!-- Main Content Grid -->
    <div class="p-6 flex-1 flex flex-col md:flex-row gap-6 mx-auto w-full max-w-6xl">
        
        <!-- Left Panel: Search & Tools -->
        <div class="w-full md:w-1/3 flex flex-col gap-6">
            <!-- Search Box -->
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h2 class="font-bold text-gray-800 mb-4 text-lg">Cari Pesanan</h2>
                <form action="{{ route('kasir.cari') }}" method="POST">
                    @csrf
                    <div class="relative">
                        <input type="text" name="kode_pesanan" placeholder="ORD-XXXXXX" value="{{ request('kode_pesanan') }}" required class="w-full border-2 border-indigo-100 bg-indigo-50/30 rounded-xl py-3 px-4 pl-11 focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition-all uppercase font-semibold tracking-wider text-indigo-900 placeholder-indigo-300">
                        <svg class="h-5 w-5 absolute left-4 top-3.5 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <button type="submit" class="w-full mt-3 bg-indigo-600 hover:bg-indigo-700 text-white py-3 rounded-xl font-bold shadow-md shadow-indigo-200 transition-all flex items-center justify-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                        Cari
                    </button>
                    @if(session('error'))
                        <div class="mt-3 text-red-500 text-sm font-medium text-center bg-red-50 py-2 rounded-lg border border-red-100">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if(session('success'))
                        <div class="mt-3 text-green-600 text-sm font-medium text-center bg-green-50 py-2 rounded-lg border border-green-100">
                            {{ session('success') }}
                        </div>
                    @endif
                </form>
            </div>

            <!-- Quick Stats or Queue (Optional visual filler) -->
            <div class="bg-indigo-50 p-6 rounded-2xl border border-indigo-100 flex-1 hidden md:block">
                <div class="text-indigo-800 text-sm mb-4">
                    <h3 class="font-bold mb-1">Panduan Status</h3>
                    <ul class="space-y-2 mt-2">
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-yellow-500"></span> <strong>Menunggu:</strong> Pesanan baru masuk</li>
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> <strong>Diterima:</strong> Sedang disiapkan dapur</li>
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-green-500"></span> <strong>Dibayar:</strong> Pembayaran lunas</li>
                        <li class="flex items-center gap-2"><span class="w-2.5 h-2.5 rounded-full bg-gray-500"></span> <strong>Selesai:</strong> Selesai total</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Right Panel: Order Details -->
        <div class="w-full md:w-2/3">
            @if(isset($order) && $order)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 mb-6 overflow-hidden flex flex-col h-full relative">
                    <!-- Order Header -->
                    <div class="bg-slate-800 text-white p-6 relative overflow-hidden">
                        <!-- Decorative bg -->
                        <div class="absolute right-0 top-0 opacity-10 pointer-events-none">
                            <svg width="200" height="200" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="100" cy="100" r="100" fill="white"/>
                            </svg>
                        </div>
                        
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <h2 class="text-3xl font-extrabold tracking-widest text-indigo-300">{{ $order->kode_pesanan }}</h2>
                                <p class="text-slate-300 flex items-center gap-1 text-sm mt-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    {{ $order->nama_pelanggan ?? 'Tamu' }}
                                    @if($order->meja)
                                        <span class="mx-2 opacity-50">•</span> Meja #{{ $order->meja->nomor_meja }}
                                    @endif
                                </p>
                            </div>
                            <div class="text-right flex flex-col items-end gap-2">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-yellow-500 text-white',
                                        'accepted' => 'bg-blue-500 text-white',
                                        'paid' => 'bg-green-500 text-white',
                                        'completed' => 'bg-gray-500 text-white'
                                    ];
                                    $colorClass = $statusColors[$order->status] ?? 'bg-gray-200 text-gray-800';
                                @endphp
                                <span class="{{ $colorClass }} px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider shadow-sm">
                                    {{ $order->status_label }}
                                </span>
                                <span class="bg-indigo-500/30 border border-indigo-400/50 text-indigo-100 text-xs px-2 py-0.5 rounded font-medium">
                                    {{ $order->tipe_order_label }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Items List -->
                    <div class="p-6 flex-1 overflow-y-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="text-xs text-gray-400 uppercase tracking-wider border-b border-gray-100">
                                    <th class="pb-3 font-semibold">Produk</th>
                                    <th class="pb-3 font-semibold text-center w-16">Qty</th>
                                    <th class="pb-3 font-semibold text-right w-28">Harga</th>
                                    <th class="pb-3 font-semibold text-right w-32">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($order->orderItems as $item)
                                    <tr class="text-gray-700">
                                        <td class="py-3 font-medium">{{ $item->produk->nama_produk }}</td>
                                        <td class="py-3 text-center"><span class="bg-gray-100 px-2.5 py-0.5 rounded text-sm font-semibold">{{ $item->qty }}</span></td>
                                        <td class="py-3 text-right text-gray-500 text-sm whitespace-nowrap">Rp {{ number_format($item->harga, 0, ',', '.') }}</td>
                                        <td class="py-3 text-right font-bold text-gray-900 whitespace-nowrap">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Summary & Actions -->
                    <div class="bg-gray-50 border-t border-gray-200 p-6">
                        <div class="flex justify-between items-center mb-6">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Metode Pembayaran</p>
                                <p class="font-bold text-gray-800 uppercase">{{ $order->metode_pembayaran }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm text-gray-500 font-medium">Total Pembayaran</p>
                                <p class="text-3xl font-black text-indigo-600">{{ $order->total_harga_formatted }}</p>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-4 border-t border-gray-200 pt-6">
                            
                            @if($order->status === 'pending')
                                <form action="{{ route('kasir.accept', $order->id) }}" method="POST" class="col-span-2">
                                    @csrf
                                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-blue-200 transition-all text-lg flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Terima Pesanan (Kirim ke Dapur)
                                    </button>
                                </form>
                            @endif

                            @if($order->status === 'accepted')
                                <div class="col-span-2 space-y-4">
                                    <form action="{{ route('kasir.bayar', $order->id) }}" method="POST" id="form-bayar">
                                        @csrf
                                        <div class="flex gap-4 items-center mb-4">
                                            <label class="text-sm font-bold text-gray-700 whitespace-nowrap">Metode:</label>
                                            <select name="metode_pembayaran" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 bg-white">
                                                <option value="cash" {{ $order->metode_pembayaran == 'cash' ? 'selected' : '' }}>Cash</option>
                                                <option value="qris" {{ $order->metode_pembayaran == 'qris' ? 'selected' : '' }}>QRIS</option>
                                                <option value="transfer" {{ $order->metode_pembayaran == 'transfer' ? 'selected' : '' }}>Transfer</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 rounded-xl shadow-lg shadow-green-200 transition-all text-lg flex items-center justify-center gap-2">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                                            Konfirmasi Pembayaran
                                        </button>
                                    </form>
                                </div>
                            @endif
                            
                            @if($order->status === 'paid')
                                <form action="{{ route('kasir.selesaikan', $order->id) }}" method="POST" class="col-span-2">
                                    @csrf
                                    <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-4 rounded-xl shadow-lg transition-all text-lg flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Selesaikan Pesanan
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="h-full border-2 border-dashed border-gray-200 rounded-2xl flex flex-col items-center justify-center p-12 text-center text-gray-400 bg-white">
                    <div class="bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <p class="text-xl font-medium text-gray-500">Cari kode pesanan</p>
                    <p class="text-sm mt-1">Masukkan kode pesanan di sebelah kiri untuk melihat detail.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
