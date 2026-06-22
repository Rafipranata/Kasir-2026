<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions;

class CreateProduk extends CreateRecord
{
    protected static string $resource = ProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected static ?string $title = 'Tambah Produk';

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
