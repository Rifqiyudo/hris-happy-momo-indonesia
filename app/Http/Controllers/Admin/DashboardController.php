<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // 1. Statistik Kartu Atas
        $totalEmployees = User::where('role', 'employee')->where('is_active', 1)->count();
        $presentToday = Attendance::whereDate('date', $today)->count();
        $lateToday = Attendance::whereDate('date', $today)->where('is_late', true)->count();
        
        // Menghitung persentase kehadiran untuk Progress Bar
        $attendancePercentage = $totalEmployees > 0 ? ($presentToday / $totalEmployees) * 100 : 0;

        // 2. Daftar Hadir Terbaru (5 orang terakhir)
        $recentAttendances = Attendance::with('user.position')
                                ->whereDate('date', $today)
                                ->orderBy('clock_in', 'desc')
                                ->take(5)
                                ->get();

        return view('admin.dashboard', compact(
            'totalEmployees', 
            'presentToday', 
            'lateToday', 
            'attendancePercentage',
            'recentAttendances'
        ));
    }
}