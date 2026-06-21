<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk #{{ $order->kode_pesanan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 12px;
            color: #111;
            background: #fff;
            width: 80mm;
            margin: 0 auto;
            padding: 8px 6px 16px;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            border-bottom: 1px dashed #555;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }
        .header .brand {
            font-size: 16px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .header .tagline {
            font-size: 10px;
            color: #555;
            margin-top: 2px;
        }

        /* ── Meta info ── */
        .meta {
            margin-bottom: 8px;
        }
        .meta table {
            width: 100%;
            border-collapse: collapse;
        }
        .meta td {
            font-size: 11px;
            padding: 1px 0;
            vertical-align: top;
        }
        .meta td:last-child {
            text-align: right;
        }

        /* ── Divider ── */
        .divider {
            border-top: 1px dashed #555;
            margin: 6px 0;
        }
        .divider-solid {
            border-top: 1px solid #111;
            margin: 6px 0;
        }

        /* ── Items ── */
        .items-header {
            display: flex;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .item-row {
            margin-bottom: 4px;
        }
        .item-name {
            font-size: 11px;
            font-weight: bold;
            word-break: break-word;
        }
        .item-detail {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            color: #333;
            padding-left: 4px;
        }

        /* ── Totals ── */
        .totals table {
            width: 100%;
            border-collapse: collapse;
        }
        .totals td {
            font-size: 11px;
            padding: 2px 0;
            vertical-align: top;
        }
        .totals td:last-child {
            text-align: right;
            white-space: nowrap;
        }
        .grand-total td {
            font-size: 13px;
            font-weight: bold;
            padding-top: 4px;
        }

        /* ── Payment ── */
        .payment-info {
            margin-top: 6px;
            font-size: 11px;
        }
        .payment-info table {
            width: 100%;
        }
        .payment-info td:last-child {
            text-align: right;
            font-weight: bold;
        }

        /* ── Footer ── */
        .footer {
            text-align: center;
            font-size: 10px;
            color: #555;
            margin-top: 12px;
            border-top: 1px dashed #555;
            padding-top: 8px;
        }
        .footer .thank-you {
            font-size: 12px;
            font-weight: bold;
            color: #111;
            margin-bottom: 3px;
        }
    </style>
</head>
<body>

    {{-- ── HEADER ── --}}
    <div class="header">
        <div class="brand">{{ $brandName }}</div>
        @if(!empty($address))
            <div class="tagline">{{ $address }}</div>
        @endif
    </div>

    {{-- ── META ORDER ── --}}
    <div class="meta">
        <table>
            <tr>
                <td>No. Transaksi</td>
                <td><strong>{{ $order->kode_pesanan }}</strong></td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td>Jam</td>
                <td>{{ $order->created_at->format('H:i:s') }}</td>
            </tr>
            <tr>
                <td>Tipe</td>
                <td>{{ $order->tipe_order === 'dine_in' ? 'Dine In' : 'Take Away' }}</td>
            </tr>
            @if($order->nama_pelanggan)
            <tr>
                <td>Pelanggan</td>
                <td>{{ $order->nama_pelanggan }}</td>
            </tr>
            @endif
            @if($order->meja)
            <tr>
                <td>Meja</td>
                <td>{{ $order->meja->nomor_meja }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="divider"></div>

    {{-- ── ITEMS ── --}}
    @foreach($order->orderItems as $item)
    <div class="item-row">
        <div class="item-name">{{ $item->produk->nama_produk ?? '-' }}</div>
        <div class="item-detail">
            <span>{{ $item->qty }} x {{ number_format($item->harga, 0, ',', '.') }}</span>
            <span>{{ number_format($item->subtotal, 0, ',', '.') }}</span>
        </div>
    </div>
    @endforeach

    <div class="divider"></div>

    {{-- ── TOTALS ── --}}
    <div class="totals">
        <table>
            <tr>
                <td>Subtotal</td>
                <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
            @if(!empty($discount) && $discount > 0)
            <tr>
                <td>Diskon</td>
                <td>- Rp {{ number_format($discount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if(!empty($tax) && $tax > 0)
            <tr>
                <td>Pajak</td>
                <td>Rp {{ number_format($tax, 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="divider-solid"></div>

    <div class="totals">
        <table>
            <tr class="grand-total">
                <td>TOTAL</td>
                <td>Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    {{-- ── PAYMENT ── --}}
    <div class="payment-info">
        <table>
            <tr>
                <td>Pembayaran</td>
                <td>{{ strtoupper($order->metode_pembayaran) }}</td>
            </tr>
            @if($order->metode_pembayaran === 'cash' && !empty($uangBayar) && $uangBayar > 0)
            <tr>
                <td>Uang Diterima</td>
                <td>Rp {{ number_format($uangBayar, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Kembalian</td>
                <td>Rp {{ number_format(max(0, $uangBayar - $order->total_harga), 0, ',', '.') }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="footer">
        <div class="thank-you">Terima Kasih!</div>
        <div>Simpan struk ini sebagai bukti pembayaran</div>
        <div style="margin-top:4px; font-size:9px; color:#888;">
            {{ now()->format('d/m/Y H:i') }} &bull; {{ $brandName }}
        </div>
    </div>

</body>
</html>
