<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Produk;
use Illuminate\Support\Facades\Storage;

echo "=== ISI gambar_produk di DB ===\n";
$produks = Produk::select('id', 'nama_produk', 'gambar_produk')->get();
foreach ($produks as $p) {
    $val = $p->gambar_produk;
    $isUrl = $val && filter_var($val, FILTER_VALIDATE_URL);
    $fileExists = null;
    if ($val && !$isUrl) {
        $fileExists = Storage::disk('public')->exists($val) ? 'ADA' : 'TIDAK ADA';
        $url = Storage::disk('public')->url($val);
    } else {
        $url = $val;
    }
    echo "ID:{$p->id} | {$p->nama_produk}\n";
    echo "  gambar_produk : " . (is_array($val) ? json_encode($val) : $val) . "\n";
    echo "  tipe          : " . gettype($val) . "\n";
    echo "  is URL        : " . ($isUrl ? 'ya' : 'tidak') . "\n";
    if ($fileExists) echo "  file di disk  : {$fileExists}\n";
    echo "  url final     : {$url}\n";
    echo "\n";
}

echo "=== Config public disk ===\n";
$diskConfig = config('filesystems.disks.public');
print_r($diskConfig);

echo "\n=== Symlink storage ===\n";
$link = public_path('storage');
echo "public/storage exists : " . (file_exists($link) ? 'ya' : 'tidak') . "\n";
echo "is_link               : " . (is_link($link) ? 'ya' : 'tidak') . "\n";
echo "points to             : " . (is_link($link) ? readlink($link) : '-') . "\n";
