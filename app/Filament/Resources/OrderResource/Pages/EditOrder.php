<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function afterSave(): void
    {
        // Hitung ulang total dari orderItems ketika edit
        $total = $this->record->orderItems()->get()->sum(function ($item) {
            $subtotal = $item->qty * $item->harga;
            $item->update(['subtotal' => $subtotal]);
            return $subtotal;
        });

        $this->record->update(['total_harga' => $total]);
    }
}
