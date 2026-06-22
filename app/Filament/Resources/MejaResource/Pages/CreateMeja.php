<?php

namespace App\Filament\Resources\MejaResource\Pages;

use App\Filament\Resources\MejaResource;
use Filament\Actions;
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

    protected static ?string $title = 'Tambah Meja';

        protected function getFormActions(): array
    {
        return [
            $this->getCreateFormAction()
                ->label('Simpan'),

            $this->getCreateAnotherFormAction()
                ->label('Simpan & Tambah Lagi')
                ->color('success')
                ->outlined(),

            $this->getCancelFormAction()
                ->label('Batal')
                ->color('danger'),
        ];
    }
}
