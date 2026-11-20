<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;        // Import Schema untuk Database
use Illuminate\Support\Facades\URL;           // Import URL untuk HTTPS
use Illuminate\Support\Facades\View;          // Import View untuk Global Variable
use Illuminate\Support\Facades\Cache;         // Import Cache untuk Performa/Optimasi
use Illuminate\Support\Facades\DB;            // Import DB untuk Query Builder
use Illuminate\Pagination\Paginator;         // Import Paginator untuk Tampilan
use Carbon\Carbon;                           // Import Carbon untuk Tanggal
use App\Models\User;                         // Import User model
use App\Observers\UserObserver;              // Import Observer yang kita buat

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ---------------------------------------------------------------------
        // 1. DATABASE COMPATIBILITY
        // ---------------------------------------------------------------------
        // Mencegah error "Specified key was too long" pada MySQL versi lama
        Schema::defaultStringLength(191);

        // ---------------------------------------------------------------------
        // 2. PAGINATION STYLE
        // ---------------------------------------------------------------------
        // Menggunakan Tailwind agar desain sidebar chat dan list karyawan tetap konsisten
        Paginator::useTailwind();

        // ---------------------------------------------------------------------
        // 3. OBSERVER REGISTRATION
        // ---------------------------------------------------------------------
        // Menghubungkan User model dengan UserObserver agar setiap user baru 
        // otomatis memiliki data percakapan chat.
        User::observe(UserObserver::class);

        // ---------------------------------------------------------------------
        // 4. LOKALISASI (BAHASA & WAKTU INDONESIA)
        // ---------------------------------------------------------------------
        config(['app.locale' => 'id']);
        
        // Mengatur format tanggal ke Bahasa Indonesia (Senin, Selasa, dll)
        Carbon::setLocale('id');
        
        // Mengatur Zona Waktu Server PHP ke WIB (Asia/Jakarta)
        // Penting agar timestamp di chat sinkron dengan waktu lokal
        date_default_timezone_set('Asia/Jakarta');

        // ---------------------------------------------------------------------
        // 5. AUTO-DETECT URL & HTTPS (SMART CONFIGURATION)
        // ---------------------------------------------------------------------
        // Mengambil APP_URL dari .env. 
        // Jika di .env kosong/tidak ada, otomatis gunakan default localhost:8000
        $appUrl = env('APP_URL', 'http://localhost:8000');
        
        if (!empty($appUrl)) {
            // Memaksa root URL mengikuti yang ada di .env
            URL::forceRootUrl($appUrl);
            
            // Otomatis force HTTPS jika APP_URL memakai https:// (DevTunnels / Ngrok)
            if (str_contains($appUrl, 'https://')) {
                URL::forceScheme('https');
            }
        }

        // ---------------------------------------------------------------------
        // 6. GLOBAL VARIABEL KONFIGURASI PERUSAHAAN (WHITE-LABELING)
        // ---------------------------------------------------------------------
        // Memastikan tabel settings ada sebelum dijalankan agar tidak error saat proses migrate awal
        if (Schema::hasTable('settings')) {
            // Menggunakan Cache (ingatan sementara) agar database tidak terbebani setiap kali user pindah halaman
            $app_settings = Cache::rememberForever('app_settings', function () {
                return DB::table('settings')->pluck('value', 'key')->toArray();
            });

            // Menyebarkan variabel $app_settings ke SELURUH file .blade.php di aplikasi
            View::share('app_settings', $app_settings);
        }
    }
}