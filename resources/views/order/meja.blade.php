@extends('layout.app')

@section('title', 'Pesanan Meja ' . $meja->nomor_meja)

@section('content')
<div x-data="cartApp()" class="pb-24 relative min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-blue-600 text-white p-5 shadow-md sticky top-0 z-50 rounded-b-2xl">
        <h1 class="text-2xl font-bold">Meja #{{ $meja->nomor_meja }}</h1>
        <p class="text-sm text-blue-100 mt-1">Silakan pilih pesanan Anda</p>
    </div>

    <!-- Menu List -->
    <div class="p-4 space-y-8">
        @foreach($kategoris as $kategori)
            @if($kategori->produks->count() > 0)
                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-800 uppercase tracking-wide">{{ $kategori->nama_kategori }}</h2>
                    <div class="grid gap-4">
                        @foreach($kategori->produks as $produk)
                            <div class="flex flex-col border border-gray-100 p-4 rounded-xl shadow-sm bg-white">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="font-semibold text-lg text-gray-800 leading-tight">{{ $produk->nama_produk }}</h3>
                                    </div>
                                    <p class="text-blue-600 font-bold whitespace-nowrap ml-2">{{ $produk->harga_formatted }}</p>
                                </div>
                                <div class="flex justify-end gap-3 mt-auto border-t border-gray-50 pt-3">
                                    <button @click="decrement({{ $produk->id }})" class="w-9 h-9 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center hover:bg-gray-200 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                    </button>
                                    <span x-text="getQty({{ $produk->id }})" class="w-8 text-center text-lg font-medium select-none">0</span>
                                    <button @click="increment({{ $produk->id }}, '{{ $produk->nama_produk }}', {{ $produk->harga_produk }})" class="w-9 h-9 rounded-full bg-blue-600 text-white flex items-center justify-center shadow-sm hover:bg-blue-700 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
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
            isSubmitting: false,
            
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
