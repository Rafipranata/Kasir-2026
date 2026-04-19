<?php

namespace App\Filament\Resources\MejaResource\Pages;

use App\Filament\Resources\MejaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMeja extends CreateRecord
{
    protected static string $resource = MejaResource::class;

    protected function afterCreate(): void
    {
        // Auto-generate QR setelah meja dibuat
        $this->record->generateQrCode();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
