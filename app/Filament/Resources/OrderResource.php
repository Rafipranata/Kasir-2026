<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use App\Models\Produk;
use App\Services\OrderService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationLabel = 'Riwayat Order';
    protected static ?string $pluralModelLabel = 'Riwayat Order';
    protected static ?string $modelLabel = 'Riwayat Order';
    protected static ?int $navigationSort = 2; // Tampil di bawah POS
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Order')
                ->schema([
                    Forms\Components\Select::make('tipe_order')
                        ->label('Tipe Order')
                        ->options([
                            'dine_in'   => 'Dine In (Makan di Tempat)',
                            'take_away' => 'Take Away (Bawa Pulang)',
                        ])
                        ->required()
                        ->reactive(),
                    Forms\Components\Select::make('meja_id')
                        ->label('Nomor Meja')
                        ->relationship('meja', 'nomor_meja')
                        ->nullable()
                        ->visible(fn ($get) => $get('tipe_order') === 'dine_in'),
                    Forms\Components\TextInput::make('nama_pelanggan')
                        ->label('Nama Pelanggan')
                        ->nullable()
                        ->visible(fn ($get) => $get('tipe_order') === 'take_away'),
                    Forms\Components\Select::make('metode_pembayaran')
                        ->label('Metode Pembayaran')
                        ->options([
                            'cash'     => 'Tunai',
                            'qris'     => 'QRIS',
                            'transfer' => 'Transfer Bank',
                            'debit'    => 'Kartu Debit',
                        ])
                        ->required(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'   => 'Pending',
                            'accepted'  => 'Diterima',
                            'paid'      => 'Dibayar',
                            'completed' => 'Selesai',
                        ])
                        ->required(),
                ])->columns(2),

            Forms\Components\Section::make('Item Pesanan')
                ->schema([
                    Forms\Components\Repeater::make('orderItems')
                        ->label('Produk')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('produk_id')
                                ->label('Produk')
                                ->options(Produk::where('ketersediaan', true)->pluck('nama_produk', 'id'))
                                ->required()
                                ->reactive()
                                ->afterStateUpdated(function ($state, callable $set) {
                                    $produk = Produk::find($state);
                                    if ($produk) {
                                        $set('harga', $produk->harga_produk);
                                    }
                                }),
                            Forms\Components\TextInput::make('qty')
                                ->label('Qty')
                                ->numeric()
                                ->required()
                                ->default(1)
                                ->minValue(1),
                            Forms\Components\TextInput::make('harga')
                                ->label('Harga')
                                ->numeric()
                                ->prefix('Rp')
                                ->disabled()
                                ->dehydrated(),
                        ])
                        ->columns(3)
                        ->minItems(1)
                        ->createItemButtonLabel('+ Tambah Item'),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Detail Order')
                ->schema([
                    Infolists\Components\TextEntry::make('kode_pesanan')
                        ->label('Kode Pesanan')
                        ->weight('bold')
                        ->size('lg')
                        ->copyable(),
                    Infolists\Components\TextEntry::make('status')
                        ->label('Status')
                        ->badge()
                        ->color(fn ($state) => match ($state) {
                            'pending'   => 'warning',
                            'accepted'  => 'info',
                            'paid'      => 'success',
                            'completed' => 'gray',
                        }),
                    Infolists\Components\TextEntry::make('tipe_order')
                        ->label('Tipe')
                        ->formatStateUsing(fn ($state) => $state === 'dine_in' ? 'Makan di Tempat' : 'Bawa Pulang'),
                    Infolists\Components\TextEntry::make('meja.nomor_meja')
                        ->label('Meja')
                        ->default('-'),
                    Infolists\Components\TextEntry::make('nama_pelanggan')
                        ->label('Nama Pelanggan')
                        ->default('-'),
                    Infolists\Components\TextEntry::make('metode_pembayaran')
                        ->label('Metode Bayar')
                        ->formatStateUsing(fn ($state) => strtoupper($state ?? '-')),
                    Infolists\Components\TextEntry::make('total_harga')
                        ->label('Total')
                        ->money('IDR')
                        ->weight('bold'),
                    Infolists\Components\TextEntry::make('created_at')
                        ->label('Waktu Order')
                        ->dateTime('d M Y H:i'),
                ])->columns(2),

            Infolists\Components\Section::make('Item Pesanan')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('orderItems')
                        ->label('')
                        ->schema([
                            Infolists\Components\TextEntry::make('produk.nama_produk')
                                ->label('Produk'),
                            Infolists\Components\TextEntry::make('qty')
                                ->label('Qty'),
                            Infolists\Components\TextEntry::make('harga')
                                ->label('Harga Satuan')
                                ->money('IDR'),
                            Infolists\Components\TextEntry::make('subtotal')
                                ->label('Subtotal')
                                ->money('IDR')
                                ->weight('bold'),
                        ])->columns(4),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_pesanan')
                    ->label('Kode Pesanan')
                    ->searchable()
                    ->copyable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('tipe_order')
                    ->label('Tipe')
                    ->formatStateUsing(fn ($state) => $state === 'dine_in' ? 'Dine In' : 'Take Away')
                    ->badge()
                    ->color(fn ($state) => $state === 'dine_in' ? 'info' : 'warning'),
                Tables\Columns\TextColumn::make('meja.nomor_meja')
                    ->label('Meja')
                    ->default('-'),
                Tables\Columns\TextColumn::make('nama_pelanggan')
                    ->label('Pelanggan')
                    ->default('-'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'pending'   => 'warning',
                        'accepted'  => 'info',
                        'paid'      => 'success',
                        'completed' => 'gray',
                        default     => 'secondary',
                    })
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pending'   => 'Menunggu',
                        'accepted'  => 'Diterima',
                        'paid'      => 'Dibayar',
                        'completed' => 'Selesai',
                        default     => $state,
                    }),
                Tables\Columns\TextColumn::make('total_harga')
                    ->label('Total')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Pembayaran')
                    ->formatStateUsing(fn ($state) => strtoupper($state ?? '-')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'   => 'Menunggu',
                        'accepted'  => 'Diterima',
                        'paid'      => 'Dibayar',
                        'completed' => 'Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('tipe_order')
                    ->label('Tipe Order')
                    ->options([
                        'dine_in'   => 'Dine In',
                        'take_away' => 'Take Away',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal')->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal')->label('Sampai Tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['dari_tanggal'], fn ($q) => $q->whereDate('created_at', '>=', $data['dari_tanggal']))
                            ->when($data['sampai_tanggal'], fn ($q) => $q->whereDate('created_at', '<=', $data['sampai_tanggal']));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('accept')
                    ->label('Accept')
                    ->icon('heroicon-o-check')
                    ->color('info')
                    ->visible(fn (Order $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->acceptOrder($record);
                        Notification::make()->title('Order diterima!')->success()->send();
                    }),
                Tables\Actions\Action::make('bayar')
                    ->label('Tandai Bayar')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->visible(fn (Order $record) => $record->status === 'accepted')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->markAsPaid($record);
                        Notification::make()->title('Order berhasil dibayar!')->success()->send();
                    }),
                Tables\Actions\Action::make('selesai')
                    ->label('Selesai')
                    ->icon('heroicon-o-check-circle')
                    ->color('gray')
                    ->visible(fn (Order $record) => $record->status === 'paid')
                    ->requiresConfirmation()
                    ->action(function (Order $record) {
                        app(OrderService::class)->completeOrder($record);
                        Notification::make()->title('Order selesai!')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
