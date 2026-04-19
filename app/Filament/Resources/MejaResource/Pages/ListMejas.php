<?php

namespace App\Filament\Resources\MejaResource\Pages;

use App\Filament\Resources\MejaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMejas extends ListRecords
{
    protected static string $resource = MejaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('export_qr')
                ->label('Export QR PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->action(function () {
                    $mejas = \App\Models\Meja::all();
                    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.meja-qr', compact('mejas'));
                    return response()->streamDownload(fn () => print($pdf->output()), 'Semua_QR_Meja.pdf');
                }),
            Actions\CreateAction::make(),
        ];
    }
}
