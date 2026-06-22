<?php

namespace App\Filament\Pages;

use App\Services\SettingService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'Settings';
    protected static ?string $title           = 'Pengaturan Aplikasi';
    protected static ?string $navigationGroup = 'Sistem';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $slug            = 'settings';

    protected static string $view = 'filament.pages.settings-page';

    /** @var array<string, mixed> */
    public array $data = [];

    /**
     * Hanya Administrator yang boleh mengakses halaman ini.
     */
    public static function canAccess(): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return $user?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        /** @var SettingService $svc */
        $svc = app(SettingService::class);

        $this->form->fill([
            'brand_name'    => $svc->get('brand_name'),
            'primary_color' => $svc->get('primary_color'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Identitas Aplikasi')
                    ->description('Ubah nama dan tampilan aplikasi.')
                    ->icon('heroicon-o-building-storefront')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('brand_name')
                                ->label('Nama Brand')
                                ->placeholder('Contoh: Kasir POS 2026')
                                ->required()
                                ->maxLength(100)
                                ->helperText('Nama ini akan tampil di header, login page, browser title, dan struk kasir.'),

                            Select::make('primary_color')
                                ->label('Warna Tema Utama')
                                ->options(self::colorOptions())
                                ->required()
                                ->helperText('Pilih warna utama aplikasi dari palette bawaan.')
                                ->allowHtml(),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        /** @var SettingService $svc */
        $svc = app(SettingService::class);

        $svc->set('brand_name', $state['brand_name']);
        $svc->set('primary_color', $state['primary_color']);

        Notification::make()
            ->success()
            ->title('Settings saved successfully')
            ->body('Perubahan akan diterapkan pada saat halaman dimuat ulang.')
            ->send();
    }

    /**
     * Daftar opsi warna dengan preview badge HTML.
     *
     * @return array<string, string>
     */
    protected static function colorOptions(): array
    {
        $colors = [
            'slate'   => ['label' => 'Slate',   'hex' => '#64748b'],
            'gray'    => ['label' => 'Gray',    'hex' => '#6b7280'],
            'zinc'    => ['label' => 'Zinc',    'hex' => '#71717a'],
            'neutral' => ['label' => 'Neutral', 'hex' => '#737373'],
            'stone'   => ['label' => 'Stone',   'hex' => '#78716c'],
            'red'     => ['label' => 'Red',     'hex' => '#ef4444'],
            'orange'  => ['label' => 'Orange',  'hex' => '#f97316'],
            'amber'   => ['label' => 'Amber',   'hex' => '#f59e0b'],
            'yellow'  => ['label' => 'Yellow',  'hex' => '#eab308'],
            'lime'    => ['label' => 'Lime',    'hex' => '#84cc16'],
            'green'   => ['label' => 'Green',   'hex' => '#22c55e'],
            'emerald' => ['label' => 'Emerald', 'hex' => '#10b981'],
            'teal'    => ['label' => 'Teal',    'hex' => '#14b8a6'],
            'cyan'    => ['label' => 'Cyan',    'hex' => '#06b6d4'],
            'sky'     => ['label' => 'Sky',     'hex' => '#0ea5e9'],
            'blue'    => ['label' => 'Blue',    'hex' => '#3b82f6'],
            'indigo'  => ['label' => 'Indigo',  'hex' => '#6366f1'],
            'violet'  => ['label' => 'Violet',  'hex' => '#8b5cf6'],
            'purple'  => ['label' => 'Purple',  'hex' => '#a855f7'],
            'fuchsia' => ['label' => 'Fuchsia', 'hex' => '#d946ef'],
            'pink'    => ['label' => 'Pink',    'hex' => '#ec4899'],
            'rose'    => ['label' => 'Rose',    'hex' => '#f43f5e'],
        ];

        $options = [];
        foreach ($colors as $key => $color) {
            $options[$key] = sprintf(
                '<span style="display:inline-flex;align-items:center;gap:8px;">
                    <span style="display:inline-block;width:16px;height:16px;border-radius:4px;background:%s;border:1px solid rgba(0,0,0,0.1);flex-shrink:0;"></span>
                    <span>%s</span>
                </span>',
                $color['hex'],
                $color['label']
            );
        }

        return $options;
    }
}
