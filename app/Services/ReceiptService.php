<?php

namespace App\Services;

use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptService
{
    /**
     * Generate PDF struk untuk order tertentu.
     *
     * @param  Order   $order      Model order dengan relasi orderItems.produk, meja
     * @param  float   $uangBayar  Uang yang diterima (opsional, untuk kasir tunai)
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generate(Order $order, float $uangBayar = 0): \Barryvdh\DomPDF\PDF
    {
        // Eager-load relasi yang dibutuhkan struk
        $order->loadMissing(['orderItems.produk', 'meja']);

        $brandName = setting('brand_name', 'Filament POS');
        $address   = setting('store_address', '');

        $pdf = Pdf::loadView('pdf.receipt', [
            'order'     => $order,
            'brandName' => $brandName,
            'address'   => $address,
            'discount'  => 0,
            'tax'       => 0,
            'uangBayar' => $uangBayar,
        ]);

        // Set ukuran halaman seperti kertas struk (80mm lebar)
        $pdf->setPaper([0, 0, 226.77, 800], 'portrait'); // 80mm = ~226.77pt

        return $pdf;
    }

    /**
     * Return PDF sebagai stream (inline di browser).
     *
     * @param  Order  $order
     * @param  float  $uangBayar
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function stream(Order $order, float $uangBayar = 0): \Symfony\Component\HttpFoundation\Response
    {
        $filename = 'struk-' . $order->kode_pesanan . '.pdf';
        return $this->generate($order, $uangBayar)->stream($filename);
    }

    /**
     * Return PDF sebagai download.
     *
     * @param  Order  $order
     * @param  float  $uangBayar
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function download(Order $order, float $uangBayar = 0): \Symfony\Component\HttpFoundation\Response
    {
        $filename = 'struk-' . $order->kode_pesanan . '.pdf';
        return $this->generate($order, $uangBayar)->download($filename);
    }
}
