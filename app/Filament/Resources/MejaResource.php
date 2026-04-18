<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MejaResource\Pages;
use App\Models\Meja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MejaResource extends Resource
{
    protected static ?string $model = Meja::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Meja';
    protected static ?string $pluralModelLabel = 'Meja';
    protected static ?string $modelLabel = 'Meja';
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('nomor_meja')
                ->label('Nomor Meja')
                ->required()
                ->maxLength(20)
                ->unique(ignoreRecord: true)
                ->placeholder('Contoh: A1, B2, VIP-1'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable()
                    ->width(60),
                Tables\Columns\TextColumn::make('nomor_meja')
                    ->label('Nomor Meja')
                    ->searchable()
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('orders_count')
                    ->label('Total Order')
                    ->counts('orders')
                    ->badge()
                    ->color('info'),
                Tables\Columns\ViewColumn::make('qr_code')
                    ->label('QR Code')
                    ->view('filament.tables.columns.qr-code'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('generate_qr')
                    ->label('Generate QR')
                    ->icon('heroicon-o-qr-code')
                    ->color('info')
                    ->action(function (Meja $record) {
                        $record->generateQrCode();
                    })
                    ->successNotificationTitle('QR Code berhasil digenerate'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMejas::route('/'),
            'create' => Pages\CreateMeja::route('/create'),
            'edit'   => Pages\EditMeja::route('/{record}/edit'),
        ];
    }
}
