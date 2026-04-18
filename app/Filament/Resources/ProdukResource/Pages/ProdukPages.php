<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;

class ListProduks extends ListRecords
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

class CreateProduk extends CreateRecord
{
    protected static string $resource = ProdukResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}

class EditProduk extends EditRecord
{
    protected static string $resource = ProdukResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    /**
     * Mencegah URL eksternal dimuat ke dalam komponen FileUpload
     * sehingga menghindari error foreach string/array.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Jika ada URL (misalnya dari Unsplash), jangan masukkan ke Form Data
        // karena FileUpload hanya bisa menangani file upload paths.
        if (isset($data['gambar_produk']) && filter_var($data['gambar_produk'], FILTER_VALIDATE_URL)) {
            $data['gambar_produk'] = null;
        }

        return $data;
    }

    /**
     * Jika user tidak upload gambar baru, pertahankan gambar lama.
     * Jika ada gambar baru (path lokal), hapus file lama dari storage.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $oldGambar = $this->record->gambar_produk;
        $newGambar = $data['gambar_produk'] ?? null;

        // Tidak ada gambar baru → pakai yang lama
        if (empty($newGambar)) {
            $data['gambar_produk'] = $oldGambar;
            return $data;
        }

        // Ada gambar baru & gambar lama adalah file lokal (bukan URL eksternal) → hapus file lama
        if ($oldGambar && !filter_var($oldGambar, FILTER_VALIDATE_URL)) {
            if ($oldGambar !== $newGambar) {
                Storage::disk('public')->delete($oldGambar);
            }
        }

        return $data;
    }
}

