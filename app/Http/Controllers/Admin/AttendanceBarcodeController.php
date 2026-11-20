<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceBarcodeController extends Controller
{
    public function index() {
        return view('admin.attendance.barcode-scanner');
    }

    public function scan(Request $request)
    {
        $user = User::where('nik', $request->nik)->first();

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'NIK Tidak Terdaftar!']);
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Cari data absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->where('date', $today)
                                ->first();

        if (!$attendance) {
            // LOGIKA MASUK
            Attendance::create([
                'user_id' => $user->id,
                'date' => $today,
                'clock_in' => $now,
                'type' => 'office',
                'status' => 'hadir' // Anda bisa tambahkan logika cek telat disini
            ]);
            return response()->json(['status' => 'success', 'message' => 'Berhasil Absen MASUK', 'name' => $user->name]);
        } 
        
        if ($attendance->clock_out == null) {
            // LOGIKA PULANG
            $attendance->update(['clock_out' => $now]);
            return response()->json(['status' => 'success', 'message' => 'Berhasil Absen PULANG', 'name' => $user->name]);
        }

        return response()->json(['status' => 'warning', 'message' => 'Anda sudah absen hari ini!']);
    }
}