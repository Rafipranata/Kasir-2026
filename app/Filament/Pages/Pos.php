<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Produk;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;

class Pos extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';
    protected static ?string $navigationLabel = 'POS Kasir';
    protected static ?string $title = 'Point of Sale (Kasir)';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 0; // Paling atas

    protected static string $view = 'filament.pages.pos';

    public $search = '';
    public $activeCategory = null;
    public $cart = [];
    public $nama_pelanggan = '';
    public $metode_pembayaran = 'cash';
    public $tipe_order = 'dine_in';
    public $uang_pelanggan = null;

    public function getCategoriesProperty()
    {
        return \App\Models\Kategori::all();
    }

    public function getProductsProperty()
    {
        return Produk::where('ketersediaan', true)
            ->when(!empty($this->search), function ($query) {
                $query->where('nama_produk', 'like', '%' . $this->search . '%');
            })
            ->when($this->activeCategory, function ($query) {
                $query->where('kategori_id', $this->activeCategory);
            })
            ->get();
    }

    public function setCategory($categoryId)
    {
        $this->activeCategory = $categoryId;
    }

    public function addToCart($productId)
    {
        $produk = Produk::find($productId);
        if (!$produk) return;

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        } else {
            $this->cart[$productId] = [
                'id' => $produk->id,
                'nama' => $produk->nama_produk,
                'harga' => $produk->harga_produk,
                'qty' => 1,
            ];
        }
    }

    public function incrementQty($productId)
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['qty']++;
        }
    }

    public function decrementQty($productId)
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['qty'] > 1) {
                $this->cart[$productId]['qty']--;
            } else {
                unset($this->cart[$productId]);
            }
        }
    }
    
    public function removeProduct($productId)
    {
        if (isset($this->cart[$productId])) {
            unset($this->cart[$productId]);
        }
    }

    public function clearCart()
    {
        $this->cart = [];
        $this->uang_pelanggan = null;
        unset($this->subtotal);
        unset($this->total);
    }

    #[\Livewire\Attributes\Computed]
    public function subtotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['harga'];
        });
    }

    #[\Livewire\Attributes\Computed]
    public function total()
    {
        return $this->subtotal;
    }

    #[\Livewire\Attributes\Computed]
    public function kembalian()
    {
        if ($this->metode_pembayaran === 'cash' && $this->uang_pelanggan !== null) {
            return max(0, (float)$this->uang_pelanggan - $this->total);
        }
        return 0;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            \Filament\Notifications\Notification::make()->title('Keranjang kosong')->danger()->send();
            return;
        }

        $this->validate([
            'tipe_order' => 'required',
            'metode_pembayaran' => 'required',
        ]);

        if ($this->metode_pembayaran === 'cash') {
            if (!$this->uang_pelanggan || $this->uang_pelanggan < $this->total) {
                \Filament\Notifications\Notification::make()->title('Uang pelanggan tidak cukup!')->danger()->send();
                return;
            }
        }

        $orderService = app(\App\Services\OrderService::class);
        $kodePesanan = $orderService->generateKodePesanan();

        DB::transaction(function () use ($kodePesanan) {
            $order = Order::create([
                'kode_pesanan' => $kodePesanan,
                'tipe_order' => $this->tipe_order,
                'nama_pelanggan' => $this->nama_pelanggan,
                'metode_pembayaran' => $this->metode_pembayaran,
                'status' => 'completed',
                'total_harga' => $this->total,
            ]);

            foreach ($this->cart as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'produk_id' => $item['id'],
                    'qty' => $item['qty'],
                    'harga' => $item['harga'],
                    'subtotal' => $item['qty'] * $item['harga'],
                ]);
            }
        });

        // Reset cart for next order instead of redirecting
        $this->clearCart();
        $this->nama_pelanggan = '';
        $this->uang_pelanggan = null;
        
        \Filament\Notifications\Notification::make()->title('Pembayaran berhasil! Order selesai.')->success()->send();
    }
}
