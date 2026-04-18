<div class="px-4 py-2">
    @if ($getRecord()->qr_code)
        <img src="data:image/svg+xml;base64,{{ $getRecord()->qr_code }}" alt="QR Code" class="w-16 h-16 rounded shadow-sm p-1 border border-gray-200 bg-white hover:scale-[2] transition-transform duration-200 cursor-zoom-in">
    @else
        <span class="text-xs text-gray-500 italic bg-gray-100 px-2 py-1 rounded">Belum ada QR</span>
    @endif
</div>
