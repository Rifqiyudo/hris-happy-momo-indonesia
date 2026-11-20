<?php

namespace App\Http\Controllers\Admin\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Attendance, Payroll, LeaveRequest, Department, Branch};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class AnalyticController extends Controller
{
    /**
     * Analitik Kehadiran Karyawan (Attendance)
     */
    public function attendance(Request $request)
    {
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        // =========================================================================
        // 1. Ringkasan Kehadiran (Menghitung Jumlah KARYAWAN UNIK dalam sebulan)
        // Agar angka tidak mencapai ratusan (karena menghitung total hari absen).
        // =========================================================================
        
        // Menghitung berapa orang (unik) yang pernah hadir dalam bulan yang dipilih
        $summaryPresent = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereNotIn('status', ['Cuti', 'Izin', 'Alpha', 'Alpha/Mangkir'])
            ->distinct('user_id') // <--- FITUR BARU: Menghitung ID yang tidak duplikat
            ->count('user_id');
            
        // Menghitung berapa orang (unik) yang pernah telat dalam bulan yang dipilih
        $summaryLate = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->where('is_late', true)
            ->distinct('user_id') // <--- FITUR BARU
            ->count('user_id');
            
        // Menghitung total Izin / Cuti
        $leaveFromRequest = LeaveRequest::where('status', 'Approved')
            ->where(function($q) use ($month, $year) {
                $q->whereMonth('start_date', $month)->whereYear('start_date', $year)
                  ->orWhereMonth('end_date', $month)->whereYear('end_date', $year);
            })
            ->distinct('user_id')
            ->count('user_id');
            
        $leaveFromAttendance = Attendance::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->whereIn('status', ['Cuti', 'Izin'])
            ->distinct('user_id')
            ->count('user_id');
            
        $summaryLeave = $leaveFromRequest + $leaveFromAttendance;

        // =========================================================================
        // 2. Trend Kehadiran Bulanan (Untuk Grafik Bar/Line)
        // Grafik TETAP MENGHITUNG TOTAL HARIAN agar visual bar chart terbentuk
        // =========================================================================
        $monthlyTrend = Attendance::select(
                DB::raw('DATE(date) as date'),
                DB::raw('COUNT(id) as total_present'),
                DB::raw('SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END) as total_late')
            )
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->groupByRaw('DATE(date)')
            ->orderByRaw('DATE(date) ASC')
            ->get();

        return view('admin.report.attendance', compact(
            'month', 'year', 'summaryPresent', 'summaryLate', 'summaryLeave', 'monthlyTrend'
        ));
    }

    /**
     * Analitik Pengeluaran Penggajian (Payroll)
     */
    public function payroll(Request $request)
    {
        $year = $request->input('year', Carbon::now()->year);

        // 1. Ringkasan Pengeluaran Gaji Tahun Ini
        $yearlyStats = Payroll::selectRaw('SUM(net_salary) as total_net')
            ->whereYear('created_at', $year)
            ->first();

        // 2. Trend Pengeluaran per Bulan (Untuk Grafik Line/Area)
        $monthlyPayrollTrend = Payroll::select(
                DB::raw('MONTH(created_at) as month'), 
                DB::raw('SUM(net_salary) as total_net')
            )
            ->whereYear('created_at', $year)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at) ASC')
            ->get()
            ->keyBy('month'); 

        // Format data bulan 1-12 agar grafik tidak terputus jika ada bulan kosong
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = [
                'month' => date("F", mktime(0, 0, 0, $i, 10)), // Nama bulan
                'total' => isset($monthlyPayrollTrend[$i]) ? $monthlyPayrollTrend[$i]->total_net : 0
            ];
        }

        return view('admin.report.payroll', compact(
            'year', 'yearlyStats', 'chartData'
        ));
    }

    /**
     * Analitik Demografi Karyawan (Employee)
     */
    public function employee()
    {
        // 1. Ringkasan Karyawan
        $totalEmployees = User::count();
        $activeEmployees = User::where('is_active', true)->count();
        $inactiveEmployees = User::where('is_active', false)->count();

        // 2. Rasio Karyawan Berdasarkan Departemen
        $departmentRatio = User::select('department_id', DB::raw('count(*) as total'))
            ->where('is_active', true) 
            ->whereNotNull('department_id') 
            ->groupBy('department_id')
            ->with('department:id,name') 
            ->get();

        // 3. Rasio Karyawan Berdasarkan Cabang
        $branchRatio = User::select('branch_id', DB::raw('count(*) as total'))
            ->where('is_active', true)
            ->whereNotNull('branch_id') 
            ->groupBy('branch_id')
            ->with('branch:id,name') 
            ->get();

        // 4. Rasio Berdasarkan Jenis Kelamin (Gender)
        $genderRatio = User::where('is_active', true)
            ->get(['sex']) 
            ->map(function($user) {
                $val = strtolower(trim($user->sex ?? ''));
                if (in_array($val, ['m', 'l', 'laki-laki', 'pria', 'male'])) return 'Laki-laki';
                if (in_array($val, ['f', 'p', 'perempuan', 'wanita', 'female'])) return 'Perempuan';
                return 'Belum Diatur'; 
            })
            ->countBy() 
            ->map(function($total, $label) {
                return (object)['gender' => $label, 'total' => $total];
            })
            ->values();

        // 5. Rasio Berdasarkan Tingkat Pendidikan (Education)
        $educationRatio = User::where('is_active', true)
            ->get(['last_education']) 
            ->map(function($user) {
                $val = strtoupper(trim($user->last_education ?? ''));
                return $val !== '' ? $val : 'Belum Diatur';
            })
            ->countBy()
            ->map(function($total, $label) {
                return (object)['education' => $label, 'total' => $total];
            })
            ->values();

        return view('admin.report.employee', compact(
            'totalEmployees', 'activeEmployees', 'inactiveEmployees', 
            'departmentRatio', 'branchRatio', 'genderRatio', 'educationRatio'
        ));
    }
}