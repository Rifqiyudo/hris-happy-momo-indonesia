<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\LeaveRequest; // Menggunakan model LeaveRequest yang konsisten dengan rute
use App\Models\Department;
use App\Models\Position;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Menampilkan Dashboard Utama Admin & PIC
     */
    public function index()
    {
        Carbon::setLocale('id');
        $today = Carbon::today();
        $user = Auth::user();

        // 1. BASE QUERY: Filter Pegawai Aktif (Penting untuk akurasi statistik)
        $baseQuery = User::where('is_active', true);
        
        // Filter khusus jika login sebagai PIC (Hanya department terkait)
        if ($user->role === 'pic') {
            $baseQuery->where('department_id', $user->department_id);
        }

        $totalKaryawan = $baseQuery->count();
        $targetEmployeeIds = $baseQuery->pluck('id');

        // 2. SUMMARY STATS (Klasifikasi SDM)
        $stats = [
            'total_staff' => (clone $baseQuery)->where('role', 'employee')->count(),
            'total_pic'   => (clone $baseQuery)->where('role', 'pic')->count(),
            'pegawai_baru'=> (clone $baseQuery)->whereMonth('join_date', Carbon::now()->month)
                                                ->whereYear('join_date', Carbon::now()->year)
                                                ->count(),
            'total_spv'   => (clone $baseQuery)->whereHas('position', function($q) {
                                $q->where('name', 'like', '%Supervisor%')->orWhere('name', 'like', '%SPV%');
                             })->count(),
        ];

        // 3. MONITORING ABSENSI HARI INI
        $attendanceToday = Attendance::whereIn('user_id', $targetEmployeeIds)
                            ->whereDate('date', $today)
                            ->get();

        $absensi = [
            'hadir'     => $attendanceToday->whereIn('status', ['hadir', 'present', 'telat'])->count(),
            'terlambat' => $attendanceToday->where('is_late', true)->count(),
            'izin'      => $attendanceToday->whereIn('status', ['izin', 'sakit'])->count(),
            'mangkir'   => max(0, $totalKaryawan - $attendanceToday->count())
        ];

        // 4. ACTIVITY FEED: Scan Kiosk Terkini
        $recentAttendances = Attendance::with(['user.position', 'user.branch'])
                            ->whereIn('user_id', $targetEmployeeIds)
                            ->whereDate('date', $today)
                            ->orderBy('created_at', 'desc')
                            ->take(8)
                            ->get();

        // 5. CHART DATA: Tren Kehadiran (7 Hari Terakhir)
        $chartDates = [];
        $chartAttendance = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartDates[] = $date->isoFormat('DD MMM');
            $chartAttendance[] = Attendance::whereIn('user_id', $targetEmployeeIds)
                                ->whereDate('date', $date)
                                ->whereIn('status', ['hadir', 'present', 'telat'])
                                ->count();
        }

        // 6. DISTRIBUSI DEPARTEMEN & TREN PAYROLL (Admin Only)
        $deptLabels = []; $deptData = [];
        $payrollDates = []; $payrollTotals = [];
        
        if ($user->role === 'admin') {
            $deptStats = Department::withCount(['users' => function($q) {
                $q->where('is_active', true);
            }])->get();
            $deptLabels = $deptStats->pluck('name');
            $deptData   = $deptStats->pluck('users_count');

            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::today()->subMonths($i);
                $payrollDates[] = $date->isoFormat('MMM Y');
                $payrollTotals[] = Payroll::where('month', $date->format('m'))
                                    ->where('year', $date->format('Y'))
                                    ->sum('net_salary');
            }
        }

        // 7. NOTIFIKASI PENDING: Pengajuan Cuti Baru (Sinkronisasi Sidebar/Notif)
        $cutiPending = LeaveRequest::whereIn('user_id', $targetEmployeeIds)
                        ->where('status', 'pending')
                        ->count();

        // 8. MONITORING JATUH TEMPO KONTRAK (Expirations)
        // Menghitung tanggal habis kontrak berdasarkan join_date + contract_duration
        $expiringToday = User::with('branch')
            ->where('employee_status', 'Contract')
            ->where('is_active', true)
            ->whereNotNull('join_date')
            ->whereNotNull('contract_duration')
            ->whereRaw('DATE_ADD(join_date, INTERVAL contract_duration MONTH) = CURRENT_DATE')
            ->get();

        $expiringSoonCount = User::where('employee_status', 'Contract')
            ->where('is_active', true)
            ->whereNotNull('join_date')
            ->whereNotNull('contract_duration')
            ->whereRaw('DATE_ADD(join_date, INTERVAL contract_duration MONTH) BETWEEN CURRENT_DATE AND DATE_ADD(CURRENT_DATE, INTERVAL 7 DAY)')
            ->count();

        // Variabel penolong untuk kompatibilitas tampilan Blade
        $hadirHariIni = $absensi['hadir'];
        $terlambat    = $absensi['terlambat'];
        $mangkir      = $absensi['mangkir'];
        $pegawaiBaru  = $stats['pegawai_baru'];

        return view('admin.dashboard', compact(
            'totalKaryawan', 'stats', 'absensi', 'hadirHariIni', 'terlambat', 'mangkir',
            'pegawaiBaru', 'recentAttendances', 'chartDates', 'chartAttendance',
            'deptLabels', 'deptData', 'payrollDates', 'payrollTotals', 'cutiPending',
            'expiringToday', 'expiringSoonCount'
        ));
    }

    /**
     * API: Widget Real-time Refresh
     */
    public function apiStats()
    {
        $today = Carbon::today();
        $user = Auth::user();

        $baseQuery = User::where('is_active', true);
        if ($user->role === 'pic') $baseQuery->where('department_id', $user->department_id);
        
        $targetIds = $baseQuery->pluck('id');
        $attendanceToday = Attendance::whereIn('user_id', $targetIds)->whereDate('date', $today)->get();

        return response()->json([
            'success' => true,
            'counts' => [
                'hadir' => $attendanceToday->whereIn('status', ['hadir', 'present', 'telat'])->count(),
                'terlambat' => $attendanceToday->where('is_late', true)->count(),
                'total' => $baseQuery->count(),
            ]
        ]);
    }
}