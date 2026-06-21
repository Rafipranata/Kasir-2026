<?php

use App\Services\SettingService;

if (! function_exists('setting')) {
    /**
     * Ambil nilai setting aplikasi secara global.
     *
     * Contoh:
     *   setting('brand_name')             // menggunakan default jika belum ada
     *   setting('brand_name', 'My Store') // menggunakan $default kustom
     *
     * @param  string|null  $key
     * @param  mixed        $default
     * @return mixed
     */
    function setting(?string $key = null, mixed $default = null): mixed
    {
        /** @var SettingService $service */
        $service = app(SettingService::class);

        if ($key === null) {
            return $service->all();
        }

        return $service->get($key, $default);
    }
}
