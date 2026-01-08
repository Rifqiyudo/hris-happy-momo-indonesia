<?php

use Illuminate\Support\Facades\Route;

// --- AUTH CONTROLLERS ---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

// --- DASHBOARD CONTROLLERS ---
use App\Http\Controllers\Admin\DashboardController; 
use App\Http\Controllers\Employee\MyDashboardController;

// --- ADMIN MASTER DATA CONTROLLERS ---
use App\Http\Controllers\Admin\Master\User\UserController;
use App\Http\Controllers\Admin\Master\ShiftController;
use App\Http\Controllers\Admin\Master\PositionController;
use App\Http\Controllers\Admin\Master\AllowanceController;

// --- ADMIN TRANSAKSI & REPORT CONTROLLERS ---
use App\Http\Controllers\Admin\Transaksi\AdminAttendanceController;
use App\Http\Controllers\Admin\Transaksi\PayrollController;
use App\Http\Controllers\Admin\Report\PrintSlipController;
use App\Http\Controllers\Admin\Report\ExportController;

// --- EMPLOYEE FEATURE CONTROLLERS ---
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\MySlipController; // TAMBAHAN BARU

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// 1. AREA TAMU (GUEST) - Belum Login
Route::middleware('guest')->group(function () {
    // Halaman Login
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');

    // Halaman Registrasi (Daftar Akun Baru)
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.process');
});

// 2. LOGOUT (Harus sudah login)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// 3. AREA ADMIN (Desktop View - Khusus Role Admin)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Dashboard Utama
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- CLUSTER MASTER DATA ---
    Route::prefix('master')->name('master.')->group(function () {
        
        // 1. User / Karyawan (CRUD Lengkap)
        Route::resource('user', UserController::class);
        
        // 2. Position / Jabatan
        Route::resource('position', PositionController::class);
        
        // 3. Shift / Jam Kerja
        Route::resource('shift', ShiftController::class);
        
        // 4. Allowance / Tunjangan
        Route::resource('allowance', AllowanceController::class);
    });

    // --- CLUSTER TRANSAKSI ---
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        
        // 1. Monitoring & Koreksi Absensi (Resource tapi tanpa create/store manual)
        // PERBAIKAN: Gunakan AdminAttendanceController, bukan AttendanceController milik employee
        Route::resource('attendance', AdminAttendanceController::class)
             ->except(['create', 'store']);
        
        // 2. Penggajian (Payroll)
        Route::get('payroll', [PayrollController::class, 'index'])->name('payroll.index');
        Route::get('payroll/create', [PayrollController::class, 'create'])->name('payroll.create'); // Form Generate
        Route::post('payroll/store', [PayrollController::class, 'store'])->name('payroll.store'); // Proses Hitung
        Route::post('payroll/{id}/publish', [PayrollController::class, 'publish'])->name('payroll.publish'); // Kirim ke Karyawan
    });

    // --- CLUSTER REPORT ---
    Route::prefix('report')->name('report.')->group(function () {
        
        // 1. Cetak Slip Gaji (PDF) - View Admin
        Route::get('slip/{id}', [PrintSlipController::class, 'printPdf'])->name('print.slip');
        
        // 2. Download Laporan Absensi (Excel)
        Route::get('export/attendance', [ExportController::class, 'exportAttendance'])->name('export.attendance');
    });
});

// 4. AREA KARYAWAN (Mobile View - Khusus Role Employee)
Route::middleware(['auth', 'role:employee'])->prefix('portal')->name('employee.')->group(function () {
    
    // Dashboard Karyawan
    Route::get('/home', [MyDashboardController::class, 'index'])->name('dashboard');
    
    // Fitur Absensi (GPS & Kamera)
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');
    
    // Fitur Slip Gaji (Lihat & Download PDF)
    Route::get('/slip', [MySlipController::class, 'index'])->name('slip.index');
    Route::get('/slip/{id}/pdf', [MySlipController::class, 'printPdf'])->name('slip.pdf');
});