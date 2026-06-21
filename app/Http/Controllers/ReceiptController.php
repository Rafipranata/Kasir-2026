<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\ReceiptService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReceiptController extends Controller
{
    public function __construct(protected ReceiptService $receiptService)
    {
    }

    /**
     * Tampilkan PDF struk secara inline (untuk iframe preview di modal).
     */
    public function stream(Order $order): Response
    {
        return $this->receiptService->stream($order);
    }

    /**
     * Download PDF struk sebagai file.
     */
    public function download(Order $order): Response
    {
        return $this->receiptService->download($order);
    }
}
