<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shift;
use App\Models\Attendance;
use Carbon\Carbon;

class MyDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil Shift Hari Ini (Logic dari Model Shift)
        $todayShift = Shift::getCurrentShift(); 

        // 2. Cek apakah hari ini sudah absen?
        $attendance = Attendance::where('user_id', $user->id)
                        ->whereDate('date', Carbon::today())
                        ->first();

        return view('employee.dashboard', compact('user', 'todayShift', 'attendance'));
    }
}