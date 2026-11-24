<?php

namespace App\Providers;

use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('rp', function (string $expression) {
            return "<?php echo 'Rp '.number_format((float) ($expression), 0, ',', '.'); ?>";
        });

        App::setLocale('id');
        Carbon::setLocale('id');

        // Pengaturan toko dari database menimpa nilai bawaan di config/store.php.
        try {
            $stored = Cache::rememberForever('store_settings', fn () => Setting::pluck('value', 'key')->all());
            $stored = array_filter($stored, fn ($v) => $v !== null && $v !== '');
            config(['store' => array_merge(config('store'), $stored)]);
        } catch (\Throwable $e) {
            // Tabel belum ada (sebelum migrate), pakai nilai bawaan.
        }
    }
}
