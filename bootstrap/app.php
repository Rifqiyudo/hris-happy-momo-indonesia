 <?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Optional: Jika ingin memisahkan route khusus Admin/PIC di file berbeda
        },
    )
    ->withMiddleware(function (Middleware $middleware) {
        
        // 1. DAFTARKAN ALIAS ROLE (Poin 7: PIC & HRD Access)
        $middleware->alias([
            // Pastikan nama class sesuai dengan file yang kita buat tadi (CheckRole)
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // 2. REDIRECTION (Jika user belum login mencoba akses admin)
        $middleware->redirectTo(
            guests: '/login',
            users: '/',
        );

        // 3. STATELESS (Optional: Untuk API Barcode di masa depan)
        $middleware->validateCsrfTokens(except: [
            // 'api/*', 
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom handling jika terjadi error hak akses (403 Forbidden)
    })->create();