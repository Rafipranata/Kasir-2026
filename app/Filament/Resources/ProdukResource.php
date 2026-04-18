<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProdukResource\Pages;
use App\Models\Kategori;
use App\Models\Produk;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?string $modelLabel = 'Produk';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Produk')
                ->schema([
                    Forms\Components\Select::make('kategori_id')
                        ->label('Kategori')
                        ->options(Kategori::pluck('nama_kategori', 'id'))
                        ->required()
                        ->searchable(),
                    Forms\Components\TextInput::make('nama_produk')
                        ->label('Nama Produk')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('harga_produk')
                        ->label('Harga')
                        ->numeric()
                        ->prefix('Rp')
                        ->required()
                        ->minValue(0),
                    Forms\Components\Toggle::make('ketersediaan')
                        ->label('Tersedia')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ])
                ->columns(2),

            Forms\Components\Section::make('Gambar Produk')
                ->schema([
                    // Tampilkan preview gambar saat ini jika nilainya adalah URL eksternal
                    Forms\Components\Placeholder::make('preview_gambar_saat_ini')
                        ->label('Gambar Saat Ini')
                        ->content(function ($record): HtmlString {
                            if (! $record?->gambar_produk) {
                                return new HtmlString('<span class="text-gray-400 text-sm">Belum ada gambar</span>');
                            }
                            $src = filter_var($record->gambar_produk, FILTER_VALIDATE_URL)
                                ? $record->gambar_produk
                                : Storage::disk('public')->url($record->gambar_produk);

                            return new HtmlString(
                                '<img src="'.e($src).'" alt="Gambar Produk" style="height:150px;width:auto;border-radius:8px;object-fit:cover;border:1px solid rgba(255,255,255,0.1)">'
                            );
                        })
                        ->columnSpanFull()
                        ->visibleOn('edit'),

                    Forms\Components\FileUpload::make('gambar_produk')
                        ->label('Ganti / Upload Gambar Baru')
                        ->image()
                        ->imageEditor()
                        ->directory('produk')
                        ->disk('public')
                        ->visibility('public')
                        ->imagePreviewHeight('200')
                        ->maxSize(2048)
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->helperText('Format: JPG, PNG, WEBP. Maks 2MB. Kosongkan jika tidak ingin mengganti gambar.')
                        ->columnSpanFull(),
                ]),
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
                Tables\Columns\ImageColumn::make('gambar_produk')
                    ->label('Foto')
                    ->disk('public')
                    ->size(60)
                    ->square()
                    ->toggleable()
                    // Handle URL eksternal (Unsplash, dll) vs path lokal di disk public
                    ->url(fn ($record) => $record?->gambar_produk
                        ? (filter_var($record->gambar_produk, FILTER_VALIDATE_URL)
                            ? $record->gambar_produk
                            : Storage::disk('public')->url($record->gambar_produk))
                        : null
                    )
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=P&background=4f46e5&color=fff&size=60'),

                Tables\Columns\TextColumn::make('nama_produk')
                    ->label('Nama Produk')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kategori.nama_kategori')
                    ->label('Kategori')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                Tables\Columns\TextColumn::make('harga_produk')
                    ->label('Harga')
                    ->formatStateUsing(fn ($state) => 'Rp '.number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\IconColumn::make('ketersediaan')
                    ->label('Tersedia')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('kategori_id')
                    ->label('Kategori')
                    ->options(Kategori::pluck('nama_kategori', 'id')),
                Tables\Filters\TernaryFilter::make('ketersediaan')
                    ->label('Ketersediaan')
                    ->trueLabel('Tersedia')
                    ->falseLabel('Tidak Tersedia'),
            ])
            ->actions([
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
            'index' => Pages\ListProduks::route('/'),
            'create' => Pages\CreateProduk::route('/create'),
            'edit' => Pages\EditProduk::route('/{record}/edit'),
        ];
    }
}
