@use('Illuminate\Support\Facades\Storage')
@extends('layout.app')

@section('title', 'Pesanan Meja ' . $meja->nomor_meja)

@section('content')
<div x-data="cartApp()" class="pb-24 relative min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="sticky top-0 z-50 bg-white/90 backdrop-blur-xl border-b border-gray-100">
        <div class="p-6 pb-2">
            <div class="flex flex-col">
                <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-blue-600 mb-1">Pesanan Aktif</span>
                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-black text-gray-900 tracking-tight">Meja #{{ $meja->nomor_meja }}</h1>
                    <div class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[10px] font-bold border border-blue-100">
                        Menu Digital
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="px-6 pb-4 space-y-4 mt-2">
            {{-- Search Bar --}}
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400 group-focus-within:text-blue-500 transition-colors" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" 
                       x-model="search"
                       class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-100 rounded-2xl text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all shadow-sm" 
                       placeholder="Cari menu favoritmu...">
            </div>

            {{-- Category Chips --}}
            <div class="flex overflow-x-auto gap-2 no-scrollbar -mx-2 px-2 pb-1">
                <button @click="activeCategory = 'all'"
                        :class="activeCategory === 'all' ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white text-gray-600 border-gray-100 hover:bg-gray-50'"
                        class="whitespace-nowrap px-5 py-2.5 rounded-xl text-xs font-bold transition-all border">
                    Semua
                </button>
                @foreach($kategoris as $kategori)
                <button @click="activeCategory = {{ $kategori->id }}"
                        :class="activeCategory === {{ $kategori->id }} ? 'bg-blue-600 text-white shadow-md shadow-blue-200' : 'bg-white text-gray-600 border-gray-100 hover:bg-gray-50'"
                        class="whitespace-nowrap px-5 py-2.5 rounded-xl text-xs font-bold transition-all border">
                    {{ $kategori->nama_kategori }}
                </button>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Menu List -->
    <div class="p-4 space-y-10">
        @foreach($kategoris as $kategori)
            @if($kategori->produks->count() > 0)
                <div x-show="showKategori({{ $kategori->id }}, {{ $kategori->produks->map(fn($p) => ['name' => $p->nama_produk])->toJson() }})" 
                     x-transition.opacity
                     class="space-y-4">
                    <h2 class="text-lg font-black text-gray-900 uppercase tracking-widest pl-1">{{ $kategori->nama_kategori }}</h2>
                    <div class="grid grid-cols-2 gap-4">
                        @foreach($kategori->produks as $produk)
                            @php
                                $gambarUrl = null;
                                if ($produk->gambar_produk) {
                                    $gambarUrl = filter_var($produk->gambar_produk, FILTER_VALIDATE_URL)
                                        ? $produk->gambar_produk
                                        : Storage::disk('public')->url($produk->gambar_produk);
                                }
                            @endphp
                            <div x-show="showProduk({{ $kategori->id }}, '{{ addslashes($produk->nama_produk) }}')"
                                 x-transition.scale.95
                                 class="flex flex-col border border-gray-100 rounded-[1.5rem] shadow-sm bg-white overflow-hidden transition-all hover:shadow-md hover:-translate-y-1">
                                {{-- Product Image --}}
                                <div class="relative w-full aspect-square bg-gray-100 overflow-hidden">
                                    @if($gambarUrl)
                                        <img src="{{ $gambarUrl }}" alt="{{ $produk->nama_produk }}" class="w-full h-full object-cover" loading="lazy">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-blue-200" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    @endif
                                    {{-- Quantity Badge --}}
                                    <div x-show="getQty({{ $produk->id }}) > 0" x-transition:enter="duration-300"
                                         class="absolute top-3 right-3 bg-blue-600 text-white text-[10px] font-black w-6 h-6 rounded-full flex items-center justify-center shadow-lg border-2 border-white">
                                        <span x-text="getQty({{ $produk->id }})"></span>
                                    </div>
                                </div>

                                {{-- Product Info --}}
                                <div class="p-3.5 flex flex-col flex-1">
                                    <h3 class="font-bold text-xs text-gray-800 leading-tight line-clamp-2 mb-2">{{ $produk->nama_produk }}</h3>
                                    <p class="text-blue-600 font-black text-sm mt-auto">{{ $produk->harga_formatted }}</p>
                                </div>

                                {{-- Quantity Controls --}}
                                <div class="flex items-center justify-between px-3 pb-4">
                                    <button @click="decrement({{ $produk->id }})" class="w-8 h-8 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center hover:bg-gray-100 hover:text-gray-600 transition-colors active:scale-90 border border-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/></svg>
                                    </button>
                                    <span x-text="getQty({{ $produk->id }})" class="w-6 text-center text-xs font-black select-none text-gray-800">0</span>
                                    <button @click="increment({{ $produk->id }}, '{{ addslashes($produk->nama_produk) }}', {{ $produk->harga_produk }})" class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-md shadow-blue-100 hover:bg-blue-700 transition-colors active:scale-90">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Cart Footer (Sticky) -->
    <div x-show="totalItems > 0" x-transition.opacity
         class="fixed bottom-0 w-full max-w-md bg-white border-t border-gray-100 p-4 shadow-[0_-10px_15px_-3px_rgba(0,0,0,0.1)] z-50 rounded-t-2xl">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm font-medium text-gray-500">Total (<span x-text="totalItems"></span> item)</p>
                <p class="text-xl font-bold text-gray-900" x-text="formatRupiah(totalPrice)"></p>
            </div>
            <button @click="openCheckoutModal = true" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-xl font-semibold shadow-lg shadow-blue-200 transition-all active:scale-95 text-lg">
                Pesan
            </button>
        </div>
    </div>

    <!-- Checkout Modal -->
    <div x-show="openCheckoutModal" style="display: none" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-0 pb-0 text-center sm:block sm:p-0">
            <div x-show="openCheckoutModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity backdrop-blur-sm" @click="openCheckoutModal = false"></div>

            <div x-show="openCheckoutModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-full"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-full"
                 class="inline-block align-bottom bg-white rounded-t-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md w-full absolute bottom-0 max-h-[90vh] flex flex-col">
                 
                <div class="bg-white px-5 pt-6 pb-4 overflow-y-auto flex-1 h-[70vh]">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-xl font-bold text-gray-900" id="modal-title">Checkout Pesanan</h3>
                        <button @click="openCheckoutModal = false" class="text-gray-400 hover:bg-gray-100 p-2 rounded-full transition-colors">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Items Summary -->
                        <div>
                            <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2 border-b pb-1">Ringkasan</h4>
                            <div class="space-y-3 font-medium">
                                <template x-for="item in Object.values(cart)" :key="item.id">
                                    <div class="flex justify-between text-gray-800">
                                        <span><span x-text="item.qty" class="text-blue-600 mr-2"></span><span x-text="item.name"></span></span>
                                        <span x-text="formatRupiah(item.qty * item.price)"></span>
                                    </div>
                                </template>
                                <div class="flex justify-between border-t border-gray-200 pt-3 text-lg font-bold">
                                    <span>Total Pembayaran</span>
                                    <span x-text="formatRupiah(totalPrice)" class="text-blue-600"></span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Data -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan (opsional)</label>
                                <input type="text" x-model="customerName" placeholder="Masukkan nama Anda" class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Pesanan</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 hover:bg-gray-50 transition-all font-medium" :class="orderType === 'dine_in' ? 'border-blue-500 ring-1 ring-blue-500' : 'border-gray-200'">
                                        <input type="radio" x-model="orderType" value="dine_in" class="sr-only">
                                        <span class="flex items-center text-sm w-full justify-center" :class="orderType === 'dine_in' ? 'text-blue-700' : 'text-gray-900'">🍽 Dine In</span>
                                    </label>
                                    <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm focus-within:ring-2 focus-within:ring-blue-500 hover:bg-gray-50 transition-all font-medium" :class="orderType === 'take_away' ? 'border-blue-500 ring-1 ring-blue-500' : 'border-gray-200'">
                                        <input type="radio" x-model="orderType" value="take_away" class="sr-only">
                                        <span class="flex items-center text-sm w-full justify-center" :class="orderType === 'take_away' ? 'text-blue-700' : 'text-gray-900'">👜 Take Away</span>
                                    </label>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                                <select x-model="paymentMethod" class="w-full border border-gray-300 rounded-xl py-3 px-4 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none appearance-none bg-white">
                                    <option value="cash">Tunai di Kasir</option>
                                    <option value="qris">QRIS</option>
                                    <option value="transfer">Transfer Bank</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gray-50 px-5 py-4 border-t border-gray-200">
                    <button @click="submitOrder()" :disabled="isSubmitting" class="w-full justify-center rounded-xl border border-transparent px-5 py-3.5 bg-blue-600 text-lg font-bold text-white hover:bg-blue-700 outline-none transition-all shadow-md shadow-blue-200 disabled:opacity-75 relative">
                        <span x-show="!isSubmitting">Kirim Pesanan Sekarang</span>
                        <span x-show="isSubmitting" class="flex justify-center items-center">
                            <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Memproses...
                        </span>
                    </button>
                    <p class="text-center text-xs text-gray-500 mt-3 flex items-center justify-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8V7a4 4 0 00-8 0v4h8z" /></svg>
                        Transaksi Aman & Terenkripsi
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function cartApp() {
        return {
            cart: {},
            openCheckoutModal: false,
            customerName: '',
            orderType: 'dine_in',
            paymentMethod: 'cash',
            search: '',
            activeCategory: 'all',

            showProduk(catId, name) {
                const matchSearch = name.toLowerCase().includes(this.search.toLowerCase());
                const matchCat = this.activeCategory === 'all' || this.activeCategory === catId;
                return matchSearch && matchCat;
            },

            showKategori(catId, produks) {
                const matchCat = this.activeCategory === 'all' || this.activeCategory === catId;
                if (!matchCat) return false;
                
                if (this.search === '') return true;
                return produks.some(p => p.name.toLowerCase().includes(this.search.toLowerCase()));
            },

            get totalItems() {
                return Object.values(this.cart).reduce((total, item) => total + item.qty, 0);
            },
            
            get totalPrice() {
                return Object.values(this.cart).reduce((total, item) => total + (item.price * item.qty), 0);
            },
            
            getQty(id) {
                return this.cart[id] ? this.cart[id].qty : 0;
            },
            
            increment(id, name, price) {
                if(this.cart[id]) {
                    this.cart[id].qty++;
                } else {
                    this.cart[id] = { id, name, price, qty: 1 };
                }
            },
            
            decrement(id) {
                if(this.cart[id]) {
                    this.cart[id].qty--;
                    if(this.cart[id].qty <= 0) {
                        delete this.cart[id];
                    }
                }
            },
            
            formatRupiah(amount) {
                return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
            },
            
            async submitOrder() {
                if (this.totalItems === 0) return;
                this.isSubmitting = true;
                
                const items = Object.values(this.cart).map(item => ({
                    produk_id: item.id,
                    qty: item.qty
                }));
                
                const payload = {
                    meja_id: {{ $meja->id }},
                    nama_pelanggan: this.customerName || 'Tamu Meja #{{ $meja->nomor_meja }}',
                    tipe_order: this.orderType,
                    metode_pembayaran: this.paymentMethod,
                    items: items,
                    _token: '{{ csrf_token() }}'
                };
                
                try {
                    const response = await fetch('{{ route('order.checkout') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(payload)
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        window.location.href = data.redirect;
                    } else {
                        Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Harap periksa kembali form anda.' });
                        this.isSubmitting = false;
                    }
                } catch (error) {
                    Swal.fire({ icon: 'error', title: 'Error', text: 'Terjadi kesalahan sistem.' });
                    this.isSubmitting = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
