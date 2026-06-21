<?php

namespace App\Services;

use App\Models\Setting;

/**
 * SettingService — Service untuk membaca dan menulis settings aplikasi.
 *
 * Penggunaan:
 *   app(SettingService::class)->get('brand_name')
 *   app(SettingService::class)->set('brand_name', 'My Store')
 *
 * Atau via helper global:
 *   setting('brand_name')
 *   setting('brand_name', 'Default Name')
 */
class SettingService
{
    /** @var array<string, mixed> In-memory cache per request */
    protected array $cache = [];

    /** Default values jika setting belum ada di database */
    protected array $defaults = [
        'brand_name'    => 'Filament POS',
        'primary_color' => 'blue',
    ];

    /**
     * Ambil nilai setting. Cache dalam request lifecycle.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        $value = Setting::get($key, $default ?? ($this->defaults[$key] ?? null));

        return $this->cache[$key] = $value;
    }

    /**
     * Simpan setting dan invalidate cache-nya.
     */
    public function set(string $key, mixed $value): void
    {
        Setting::set($key, $value);
        $this->cache[$key] = $value;
    }

    /**
     * Ambil semua settings (merge default + database).
     */
    public function all(): array
    {
        $fromDb = Setting::getAll();

        return array_merge($this->defaults, $fromDb);
    }

    /**
     * Invalidate in-memory cache.
     */
    public function flush(): void
    {
        $this->cache = [];
    }

    /**
     * Mapping nama warna string ke Filament Color constant.
     *
     * @return array<string, int[]>|null
     */
    public function resolveColor(string $colorName): ?array
    {
        $map = [
            'slate'   => \Filament\Support\Colors\Color::Slate,
            'gray'    => \Filament\Support\Colors\Color::Gray,
            'zinc'    => \Filament\Support\Colors\Color::Zinc,
            'neutral' => \Filament\Support\Colors\Color::Neutral,
            'stone'   => \Filament\Support\Colors\Color::Stone,
            'red'     => \Filament\Support\Colors\Color::Red,
            'orange'  => \Filament\Support\Colors\Color::Orange,
            'amber'   => \Filament\Support\Colors\Color::Amber,
            'yellow'  => \Filament\Support\Colors\Color::Yellow,
            'lime'    => \Filament\Support\Colors\Color::Lime,
            'green'   => \Filament\Support\Colors\Color::Green,
            'emerald' => \Filament\Support\Colors\Color::Emerald,
            'teal'    => \Filament\Support\Colors\Color::Teal,
            'cyan'    => \Filament\Support\Colors\Color::Cyan,
            'sky'     => \Filament\Support\Colors\Color::Sky,
            'blue'    => \Filament\Support\Colors\Color::Blue,
            'indigo'  => \Filament\Support\Colors\Color::Indigo,
            'violet'  => \Filament\Support\Colors\Color::Violet,
            'purple'  => \Filament\Support\Colors\Color::Purple,
            'fuchsia' => \Filament\Support\Colors\Color::Fuchsia,
            'pink'    => \Filament\Support\Colors\Color::Pink,
            'rose'    => \Filament\Support\Colors\Color::Rose,
        ];

        return $map[$colorName] ?? $map['blue'];
    }
}
