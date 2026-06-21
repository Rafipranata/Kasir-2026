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
    <div
    x-data="{
        open: false,
        orderId: null,
        uangBayar: 0,
        streamUrl: '',
        downloadUrl: '',

        init() {
            /* Dengarkan event dari Livewire */
            $wire.on('open-receipt-modal', ({ orderId, uangBayar }) => {
                this.orderId     = orderId;
                this.uangBayar   = uangBayar;
                this.streamUrl   = '/receipt/' + orderId + '/stream';
                this.downloadUrl = '/receipt/' + orderId + '/download';
                this.open        = true;
            });
        },

        printReceipt() {
            const iframe = document.getElementById('receipt-iframe');
            if (iframe) {
                iframe.contentWindow.focus();
                iframe.contentWindow.print();
            }
        },

        close() {
            this.open = false;
            @this.closeReceiptModal();
        }
    }"
    x-show="open"
    x-cloak
    style="display:none;"
    class="fixed inset-0 z-[9999] flex items-center justify-center"
>
    {{-- Backdrop --}}
    <div
        class="absolute inset-0 bg-black/60 backdrop-blur-sm"
        @click="close()"
    ></div>

    {{-- Modal Box --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-sm mx-4 overflow-hidden"
        style="max-height: 92vh;"
    >
        {{-- Header Modal --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
            <div class="flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 14.25l6-6m4.5-3.493V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0111.186 0c1.1.128 1.907 1.077 1.907 2.185z" />
                </svg>
                <h3 class="text-base font-bold text-gray-900 dark:text-white">Struk Pembayaran</h3>
            </div>
            <button @click="close()" class="p-1.5 rounded-lg text-gray-500 hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- PDF Preview --}}
        <div class="bg-gray-100 dark:bg-gray-950" style="height: 55vh;">
            <iframe
                id="receipt-iframe"
                :src="streamUrl"
                class="w-full h-full border-0"
                title="Struk Pembayaran"
            ></iframe>
        </div>

        {{-- Action Buttons --}}
        <div class="flex gap-3 p-4 bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            {{-- Print --}}
            <button
                @click="printReceipt()"
                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-primary-600 hover:bg-primary-500 text-white font-semibold text-sm transition-all shadow hover:shadow-md active:scale-95"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.041 48.041 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z" />
                </svg>
                Print
            </button>

            {{-- Download --}}
            <a
                :href="downloadUrl"
                target="_blank"
                class="flex-1 flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-success-600 hover:bg-success-500 text-white font-semibold text-sm transition-all shadow hover:shadow-md active:scale-95"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                Download
            </a>

            {{-- Close --}}
            <button
                @click="close()"
                class="flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-gray-100 hover:bg-gray-200 dark:bg-gray-800 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 font-semibold text-sm transition-all active:scale-95"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
                Tutup
            </button>
        </div>
    </div>
</div>
</x-filament-panels::page>

{{-- ═══════════════════════════════════════════════════════════
     MODAL STRUK — muncul otomatis setelah checkout berhasil
═══════════════════════════════════════════════════════════ --}}

