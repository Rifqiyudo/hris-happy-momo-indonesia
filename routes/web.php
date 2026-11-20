<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// ============================================================================
// DAFTAR IMPORT CONTROLLER
// ============================================================================

// --- AUTH & CORE ---
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Employee\MyDashboardController;

// --- ADMIN: MASTER DATA ---
use App\Http\Controllers\Admin\{ProfileController, LeaveAllowanceController};
use App\Http\Controllers\Admin\Gaji\GajiController;
use App\Http\Controllers\Admin\Jadwal\WeeklyScheduleController;
use App\Http\Controllers\Admin\Master\{
    ShiftController, PositionController, AllowanceController, 
    BranchController, DepartmentController, AnnouncementController
};
use App\Http\Controllers\Admin\Master\User\{
    UserController, 
    EmployeeDocumentController, 
    EmployeeAssetController
};
use App\Http\Controllers\Admin\Master\KpiIndicatorController;

// --- ADMIN: TRANSAKSI & REPORTS ---
use App\Http\Controllers\Admin\Transaksi\{
    AdminAttendanceController, 
    PayrollController, 
    LeaveRequestController, 
    AssetController,
    ReimbursementController as AdminReimbursementController
};
// [FITUR BARU] Import Controller Lembur untuk Admin
use App\Http\Controllers\Admin\OvertimeController as AdminOvertimeController;
use App\Http\Controllers\Admin\Transaksi\KpiController;
use App\Http\Controllers\Admin\Report\{PrintSlipController, ExportController, AnalyticController};
use App\Http\Controllers\Admin\Settings\{SettingController, RoleController};

// --- CHAT SYSTEM ---
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Employee\ChatController as EmployeeChatController;

// --- EMPLOYEE: FITUR MANDIRI (ESS) ---
use App\Http\Controllers\Employee\{
    MyHistoryController, MyPayrollController, MyLeaveController,
    MyOvertimeController, MyReimbursementController, MyProfileController, MyAttendanceController
};

/*
|--------------------------------------------------------------------------
| Web Routes Configuration
|--------------------------------------------------------------------------
*/

// ============================================================================
// 1. ROOT & AUTENTIKASI
// ============================================================================
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        return ($user->role === 'admin' || $user->role === 'pic') 
            ? redirect()->route('admin.dashboard') 
            : redirect()->route('employee.dashboard');
    }
    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.process');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

/**
 * ============================================================================
 * 2. ADMINISTRATOR & PIC PORTAL
 * ============================================================================
 */
Route::middleware(['auth', 'role:admin,pic'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // --- CHAT SYSTEM ADMIN ---
    Route::prefix('chat')->name('chat.')->controller(AdminChatController::class)->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/search', 'searchEmployee')->name('search');
        Route::get('/start/{userId}', 'startChat')->name('start');
        Route::get('/{chat}', 'show')->name('show');
        Route::post('/{chat}', 'store')->name('store');
    });

    Route::controller(ProfileController::class)->prefix('profile')->name('profile.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::put('/', 'update')->name('update');
    });

    Route::post('/leave-allowance/update', [LeaveAllowanceController::class, 'updateQuota'])->name('leave-allowance.update');
    
    // --- GAJI ---
    Route::controller(GajiController::class)->prefix('gaji')->name('gaji.')->group(function() {
        // [FITUR BARU] Export Excel/CSV ditambahkan di sini
        Route::get('/export', 'exportData')->name('export');
        Route::get('/download-template', 'downloadTemplate')->name('download-template');
        Route::post('/import-process', 'importProcess')->name('import.process');
    });
    Route::resource('gaji', GajiController::class);

    // --- DOKUMEN & ASET PROFIL KARYAWAN ---
    Route::controller(EmployeeDocumentController::class)->prefix('documents')->name('documents.')->group(function() {
        Route::post('/upload/{user}', 'upload')->name('upload'); 
        Route::post('/store/{user}', 'upload')->name('store');
        Route::get('/download/{id}', 'download')->name('download');
        Route::delete('/delete/{id}', 'destroy')->name('delete');
        Route::delete('/destroy/{id}', 'destroy')->name('destroy');
    });

    Route::post('/employee-assets/return/{id}', [EmployeeAssetController::class, 'returnAsset'])->name('employee-assets.return'); 

    // --- MASTER DATA ---
    Route::prefix('master')->name('master.')->group(function () {
        
        Route::controller(UserController::class)->prefix('user')->name('user.')->group(function() {
            Route::get('/download-template', 'downloadTemplate')->name('download-template');
            Route::get('/import', 'importForm')->name('import');
            Route::post('/import', 'importProcess')->name('import.process');
            Route::get('/report/contract-pdf', 'printContractReport')->name('report-pdf');
            Route::get('/{id}/preview-card', 'previewCard')->name('preview-card');
            Route::get('/{id}/print-card', 'printCard')->name('print-card');
            
            // [FITUR BARU] Cetak Halaman Profil Karyawan (A4)
            Route::get('/{id}/print', 'print')->name('print');
        });

        Route::resources([
            'user'          => UserController::class,
            'branch'        => BranchController::class,
            'position'      => PositionController::class,
            'allowance'     => AllowanceController::class,
            'department'    => DepartmentController::class,
            'announcement'  => AnnouncementController::class,
            'kpi-indicator' => KpiIndicatorController::class,
        ]);
        
        Route::patch('announcement/{id}/toggle', [AnnouncementController::class, 'toggleStatus'])->name('announcement.toggle');

        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::controller(WeeklyScheduleController::class)->prefix('weekly')->name('weekly.')->group(function() {
                Route::get('/', 'index')->name('index');
                Route::post('/update', 'updatePola')->name('update'); 
            });
            Route::controller(ShiftController::class)->prefix('shift')->name('shift.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');
                Route::put('/pattern/update', 'updatePattern')->name('pattern.update');
                Route::put('/{id}', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('destroy');
            });
        });
    });

    // --- TRANSAKSI & AKTIVITAS ---
    Route::prefix('transaksi')->name('transaksi.')->group(function () {
        
        Route::prefix('asset')->name('asset.')->controller(AssetController::class)->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/create', 'create')->name('create');
            Route::post('/store', 'store')->name('store');
            Route::post('/assign', 'assign')->name('assign');
            Route::get('/{id}', 'show')->name('show');
            Route::get('/{id}/edit', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/return/{id}', 'returnAsset')->name('return'); 
        });

        Route::controller(AdminAttendanceController::class)->prefix('attendance')->name('attendance.')->group(function() {
            // Rute Kiosk (Harian)
            Route::get('/scan', 'kioskPage')->name('scan');
            Route::post('/process-scan', 'processScan')->name('process-scan');
            Route::get('/monitoring', 'monitoring')->name('monitoring');
            
            // [FITUR BARU] Rute Grid Kolektif & Excel (Bulanan)
            Route::get('/', 'index')->name('index');
            Route::post('/store-kolektif', 'storeKolektif')->name('storeKolektif');
            Route::get('/export', 'exportExcel')->name('export');
            
            // [FITUR BARU] CETAK PDF REKAP ABSENSI
            Route::get('/export-pdf', 'exportPdf')->name('exportPdf');
            
            Route::post('/import', 'importExcel')->name('import');
            
            // [FITUR BARU] Rute Preview Instan Excel via AJAX (Modal Import)
            Route::post('/preview-excel', 'previewExcel')->name('preview');
            
            // [FITUR BARU] Rute Reset Data Bulanan
            Route::post('/reset-monthly', 'resetMonthly')->name('resetMonthly');
            
            // Rute CRUD Detail (Harian)
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });
        
        Route::prefix('payroll')->name('payroll.')->group(function() {
            Route::get('/', [PayrollController::class, 'index'])->name('index');
            Route::get('/create', [PayrollController::class, 'create'])->name('create'); 
            Route::post('/generate-mass', [PayrollController::class, 'generateMass'])->name('generate-mass'); 
            Route::get('/{id}', [PayrollController::class, 'show'])->name('show');
            Route::post('/{id}/publish', [PayrollController::class, 'publish'])->name('publish');
            Route::get('/{id}/print-slip', [PayrollController::class, 'printSlip'])->name('print-slip'); 
            Route::delete('/{id}', [PayrollController::class, 'destroy'])->name('destroy');
        });
        
        Route::controller(LeaveRequestController::class)->prefix('leave')->name('leave.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
        });

        // REIMBURSEMENT / KASBON
        Route::controller(AdminReimbursementController::class)->prefix('reimbursement')->name('reimbursement.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
            
            // [FITUR BARU] Update Status Persetujuan Klaim (Setuju / Tolak) via Form
            Route::put('/{id}/status', 'updateStatus')->name('update-status');
        });

        // [FITUR BARU] MANAJEMEN LEMBUR ADMIN (APPROVAL & MONITORING)
        Route::controller(AdminOvertimeController::class)->prefix('overtime')->name('overtime.')->group(function() {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/approve', 'approve')->name('approve');
            Route::post('/{id}/reject', 'reject')->name('reject');
        });

        Route::get('kpi/{id}/print', [KpiController::class, 'print'])->name('kpi.print');
        Route::resource('kpi', KpiController::class); 
    });

    // --- LAPORAN & ANALITIK ---
    Route::prefix('report')->name('report.')->group(function() {
        Route::get('slip/{id}', [PrintSlipController::class, 'printPdf'])->name('print.slip');
        Route::get('export/attendance/excel', [ExportController::class, 'exportAttendanceExcel'])->name('export.attendance.excel');
        
        // Menu Analitik
        Route::controller(AnalyticController::class)->group(function() {
            Route::get('/attendance', 'attendance')->name('attendance');
            Route::get('/payroll', 'payroll')->name('payroll');
            Route::get('/employee', 'employee')->name('employee');
        });
    });

    // --- PENGATURAN SISTEM ---
    Route::prefix('settings')->name('settings.')->middleware('role:admin')->group(function() {
        
        Route::controller(SettingController::class)->group(function() {
            Route::get('/role', 'role')->name('role');
            Route::get('/company', 'company')->name('company');
            Route::post('/company/update', 'updateCompany')->name('company.update');
            
            // [FITUR BARU] Route Export Data Audit Log
            Route::get('/audit/export', 'exportAudit')->name('audit.export');
            
            Route::get('/audit', 'audit')->name('audit');
        });

        // Mengelola Hak Akses (Permissions) Spatie
        Route::controller(RoleController::class)->prefix('role')->name('role.')->group(function() {
            
            // [FITUR BARU] Tambahan rute POST langsung ke /admin/settings/role untuk menangkap form yang submit tanpa parameter /store di url-nya
            Route::post('/', 'store')->name('store.direct'); 
            
            // (Lama - Tidak dihapus)
            Route::post('/store', 'store')->name('store');
            Route::put('/update/{id}', 'update')->name('update');
        });
        
    });
});

/**
 * ============================================================================
 * 3. EMPLOYEE PORTAL (ESS - Employee Self Service)
 * ============================================================================
 */
Route::middleware(['auth', 'role:employee,pic'])->prefix('employee')->name('employee.')->group(function () {
    
    Route::get('/dashboard', [MyDashboardController::class, 'index'])->name('dashboard');
    
    // --- FITUR CHAT & PUSAT BANTUAN HRD ---
    Route::get('/message', [EmployeeChatController::class, 'index'])->name('message.index');
    Route::prefix('chat')->name('chat.')->controller(EmployeeChatController::class)->group(function() {
        Route::get('/', 'index')->name('index'); 
        Route::get('/{chat}', 'show')->name('show');
        Route::post('/{chat}', 'store')->name('store');
    });
    
    // --- ABSENSI ---
    Route::controller(MyAttendanceController::class)->prefix('attendance')->name('attendance.')->group(function() {
        Route::get('/create', 'faceVisit')->name('create');
        Route::post('/store-visit', 'storeVisit')->name('store-visit');
    });

    // --- RIWAYAT KEHADIRAN ---
    Route::controller(MyHistoryController::class)->prefix('history')->name('history.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/pdf', 'downloadPdf')->name('pdf');
    });

    // --- PENGAJUAN CUTI & IZIN ---
    Route::controller(MyLeaveController::class)->prefix('leave')->name('leave.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/store', 'store')->name('store');
        Route::get('/{id}', 'show')->name('show'); 
        Route::delete('/{id}/cancel', 'destroy')->name('destroy');
    });

    // --- LEMBUR & KLAIM KASBON ---
    Route::resources([
        'overtime'      => MyOvertimeController::class,
        'reimbursement' => MyReimbursementController::class, 
    ]);

    // --- PAYROLL / GAJI ---
    Route::controller(MyPayrollController::class)->prefix('payroll')->name('payroll.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/{id}/pdf', 'printPdf')->name('print');
    });

    // --- PROFIL SAYA ---
    Route::controller(MyProfileController::class)->prefix('profile')->name('profile.')->group(function() {
        Route::get('/', 'index')->name('index');
        Route::get('/edit', 'edit')->name('edit');
        Route::put('/', 'update')->name('update');
        Route::get('/certificate/download', 'downloadCertificate')->name('certificate');
    });
});