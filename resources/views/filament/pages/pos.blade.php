<x-filament-panels::page>
    <div class="flex flex-col md:flex-row gap-6">

        <!-- Area Produk (Kiri) -->
        <div class="flex-1">
            <!-- Search bar -->
            <div class="mb-4">
                <input type="text" wire:model.live="search" placeholder="Cari Menu Makanan & Minuman"
                    class="w-full bg-white dark:bg-gray-800 border-gray-300 dark:border-gray-700 rounded-lg shadow-sm focus:border-primary-500 focus:ring-primary-500">
            </div>

            <!-- Grid Produk -->
            <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($this->products as $produk)
                    <!-- Gunakan wire:click pada parent card dan style cursor-pointer -->
                    <div wire:click="addToCart({{ $produk->id }})"
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer hover:ring-2 hover:ring-primary-500 transition-all flex flex-col">
                        <div class="h-32 bg-gray-100 dark:bg-gray-900 overflow-hidden relative">
                            @php
                                $isUrl = filter_var($produk->gambar_produk, FILTER_VALIDATE_URL);
                                $imgSrc = $produk->gambar_produk
                                    ? ($isUrl
                                        ? $produk->gambar_produk
                                        : asset('storage/' . $produk->gambar_produk))
                                    : 'https://ui-avatars.com/api/?name=' .
                                        urlencode($produk->nama_produk) .
                                        '&background=4f46e5&color=fff';
                            @endphp

                            {{-- Class w-full h-full dan object-cover akan membuat gambar memenuhi container tanpa merusak rasio --}}
                            <img src="{{ $imgSrc }}" alt="{{ $produk->nama_produk }}"
                                class="w-full h-full object-cover">

                            @if (!$produk->ketersediaan)
                                <div
                                    class="absolute inset-0 bg-black/50 flex items-center justify-center backdrop-blur-[1px]">
                                    <span
                                        class="text-white font-bold bg-danger-600 px-2 py-1 rounded text-xs uppercase tracking-wider">Habis</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-3 flex flex-col flex-grow">
                            <span class="text-primary-600 dark:text-primary-400 font-semibold text-xs mb-1">
                                IDR {{ number_format($produk->harga_produk, 0, ',', '.') }}
                            </span>
                            <h3
                                class="font-bold text-gray-900 dark:text-white line-clamp-2 text-sm leading-tight flex-grow">
                                {{ $produk->nama_produk }}
                            </h3>
                            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                                {{ $produk->ketersediaan ? 'Tersedia' : 'Habis' }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-10 text-gray-500 bg-white dark:bg-gray-800 rounded-xl">
                        Tidak ada produk ditemukan.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Area Keranjang (Kanan) -->
        <div class="md:w-[320px] lg:w-[400px] shrink-0 flex flex-col gap-4">

            <div
                class="bg-white dark:bg-gray-800 rounded-xl  shadow-sm border border-gray-200 dark:border-gray-700 p-4 sticky top-6">
                <!-- Header Cart -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-bold">Detail pesanan</h2>
                    <button wire:click="clearCart"
                        class="text-xs px-2 py-1 bg-danger-100 text-danger-600 dark:bg-danger-500/20 rounded hover:bg-danger-200 transition">
                        Bersihkan
                    </button>
                </div>

                <!-- Input Pelanggan -->
                <div class="mb-3">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Nama
                        Customer</label>
                    <input type="text" wire:model="nama_pelanggan" placeholder="Nama Pelanggan"
                        class="w-full text-sm bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-lg">
                </div>

                <!-- Tipe Order & Metode Pembayaran -->
                <div class="grid grid-cols-2 mt-2 gap-2 mb-4">
                    <div>
                        <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Tipe</label>
                        <select wire:model="tipe_order"
                            class="w-full text-sm bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-lg">
                            <option value="dine_in">Dine In</option>
                            <option value="take_away">Take Away</option>
                        </select>
                    </div>
                    <div>
                        <label
                            class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Pembayaran</label>
                        <select wire:model.live="metode_pembayaran"
                            class="w-full text-sm bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-lg">
                            <option value="cash">Tunai</option>
                            <option value="qris">QRIS</option>
                            <option value="transfer">Transfer</option>
                            <option value="debit">Debit</option>
                        </select>
                    </div>
                </div>

                @if($metode_pembayaran === 'cash')
                <!-- Input Uang Pelanggan -->
                <div class="mb-4" x-data="{ 
                    raw: $wire.entangle('uang_pelanggan').live,
                    formatted: ''
                }" x-init="
                    formatted = raw ? new Intl.NumberFormat('id-ID').format(raw) : '';
                    $watch('raw', value => {
                        if(!value) formatted = '';
                    });
                ">
                    <label class="block text-xs font-semibold mb-1 text-gray-600 dark:text-gray-400">Uang Pelanggan</label>
                    <div class="relative">
                        {{-- <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-500 text-sm font-semibold">Rp</span> --}}
                        <input type="text" 
                            x-model="formatted"
                            @input="
                                let val = $event.target.value.replace(/[^0-9]/g, '');
                                formatted = val ? new Intl.NumberFormat('id-ID').format(val) : '';
                                raw = val ? parseInt(val) : null;
                            "
                            placeholder="0"
                            class="w-full pl-9 text-sm font-bold bg-gray-50 dark:bg-gray-900 border-gray-300 dark:border-gray-700 rounded-lg focus:border-primary-500 focus:ring-primary-500">
                    </div>
                </div>
                @endif

                <!-- Daftar Item -->
                <div
                    class="bg-gray-50/50 dark:bg-gray-900/40 rounded-xl p-3 min-h-[180px] max-h-[350px] overflow-y-auto mb-4 border border-gray-200/60 dark:border-gray-700/50 shadow-inner custom-scrollbar">
                    @forelse($cart as $id => $item)
                        <div
                            class="flex items-center justify-between py-3 border-b border-gray-200 dark:border-gray-800 last:border-0 group transition-all">
                            <div class="flex-1 pr-3 min-w-0">
                                <h4
                                    class="text-sm font-bold text-gray-800 dark:text-gray-200 truncate group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">
                                    {{ $item['nama'] }}
                                </h4>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span
                                        class="text-[11px] font-bold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-950/30 px-1.5 rounded">
                                        Rp {{ number_format($item['harga'] * $item['qty'], 0, ',', '.') }}
                                    </span>
                                </div>
                            </div>

                            <div
                                class="flex items-center bg-white dark:bg-gray-800 rounded-lg p-1 shadow-sm border border-gray-200 dark:border-gray-700">
                                <button wire:click="decrementQty({{ $id }})"
                                    class="w-7 h-7 rounded-md flex items-center justify-center text-gray-500 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-danger-900/20 transition-all active:scale-90">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M20 12H4"></path>
                                    </svg>
                                </button>

                                <span class="w-8 text-center text-sm font-black text-gray-900 dark:text-white">
                                    {{ $item['qty'] }}
                                </span>

                                <button wire:click="incrementQty({{ $id }})"
                                    class="w-7 h-7 rounded-md bg-primary-50 dark:bg-primary-900/30 flex items-center justify-center text-primary-600 dark:text-primary-400 hover:bg-primary-600 hover:text-white dark:hover:bg-primary-500 transition-all active:scale-90 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @empty
                        <div
                            class="h-full min-h-[150px] flex flex-col items-center justify-center text-gray-400 dark:text-gray-600 py-6">
                            <div class="p-4 bg-gray-100 dark:bg-gray-800 rounded-full mb-3">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <p class="text-xs font-bold uppercase tracking-widest">Keranjang Kosong</p>
                            <p class="text-[10px] mt-1 italic">Silakan pilih produk di sebelah kiri</p>
                        </div>
                    @endforelse
                </div>

                <!-- Ringkasan Harga -->
                <div class="space-y-2 mb-4 text-sm px-1">
                    <div class="flex justify-between text-gray-500">
                        <span>Sub total</span>
                        <span class="font-medium text-gray-900 dark:text-gray-100">IDR
                            {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="border-t border-gray-200 dark:border-gray-700 pt-3 mb-4">
                    <div class="flex justify-between items-center text-lg font-bold">
                        <span>Total</span>
                        <span class="text-primary-600 dark:text-primary-400">IDR
                            {{ number_format($this->total, 0, ',', '.') }}</span>
                    </div>
                    
                    @if($metode_pembayaran === 'cash' && $uang_pelanggan)
                    <div class="flex justify-between items-center text-sm mt-2 font-semibold border-t border-dashed border-gray-200 dark:border-gray-700 pt-2">
                        <span class="text-gray-600 dark:text-gray-400">Kembalian</span>
                        <span class="{{ $this->kembalian > 0 ? 'text-success-600 dark:text-success-400' : 'text-gray-600 dark:text-gray-400' }}">IDR
                            {{ number_format($this->kembalian, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <!-- Tombol Proses -->
                <button wire:click="checkout" @if (empty($cart)) disabled @endif
                    class="w-full py-3 rounded-lg font-bold text-white transition-all {{ empty($cart) ? 'bg-gray-400 cursor-not-allowed' : 'bg-primary-600 hover:bg-primary-500 shadow-md hover:shadow-lg' }}">
                    Proses pembayaran
                </button>

            </div>
        </div>
    </div>
</x-filament-panels::page>
