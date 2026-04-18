<!DOCTYPE html>
<html>
<head>
    <title>Daftar QR Code Meja</title>
    <style>
        body { 
            font-family: sans-serif; 
            text-align: center; 
        }
        h1 {
            margin-bottom: 30px;
        }
        .container-table { 
            width: 100%; 
            border-collapse: collapse;
        }
        .container-table td {
            width: 50%;
            vertical-align: top;
            padding: 10px;
        }
        .card { 
            width: 100%; 
            border: 2px dashed #000; 
            padding: 15px; 
            box-sizing: border-box; 
            page-break-inside: avoid;
        }
        .title { 
            font-size: 24px; 
            font-weight: bold; 
            margin-bottom: 10px; 
        }
        .qr-img { 
            width: 150px; 
            height: 150px; 
        }
    </style>
</head>
<body>
    <h1>Daftar QR Code Semua Meja</h1>

    @php
        $half = ceil($mejas->count() / 2);
        // Menggunakan method slice kemudian values index agar urut 0, 1, 2...
        $col1 = $mejas->slice(0, $half)->values();
        $col2 = $mejas->slice($half)->values();
    @endphp

    <table class="container-table">
        @for ($i = 0; $i < $half; $i++)
            <tr>
                <td align="center">
                    @if(isset($col1[$i]))
                        <div class="card">
                            <div class="title">Meja {{ $col1[$i]->nomor_meja }}</div>
                            @php
                                $url = url('/order/meja/' . $col1[$i]->id);
                                $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(1)->generate($url);
                                $base64 = base64_encode($svg);
                            @endphp
                            <img src="data:image/svg+xml;base64,{!! $base64 !!}" class="qr-img" alt="QR Meja {{ $col1[$i]->nomor_meja }}">
                        </div>
                    @endif
                </td>
                <td align="center">
                    @if(isset($col2[$i]))
                        <div class="card">
                            <div class="title">Meja {{ $col2[$i]->nomor_meja }}</div>
                            @php
                                $url = url('/order/meja/' . $col2[$i]->id);
                                $svg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(1)->generate($url);
                                $base64 = base64_encode($svg);
                            @endphp
                            <img src="data:image/svg+xml;base64,{!! $base64 !!}" class="qr-img" alt="QR Meja {{ $col2[$i]->nomor_meja }}">
                        </div>
                    @endif
                </td>
            </tr>
        @endfor
    </table>
</body>
</html>
