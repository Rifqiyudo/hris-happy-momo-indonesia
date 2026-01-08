<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // KOORDINAT KANTOR (GANTI DENGAN TITIK REAL GUDANG ANDA)
    // Contoh: Margomulyo Permai
    protected $officeLat = -7.245842; 
    protected $officeLng = 112.678456;
    protected $radiusKm = 0.5; // Radius 500 meter

    public function index()
    {
        $attendance = Attendance::where('user_id', Auth::id())
                        ->whereDate('date', Carbon::today())
                        ->first();
                        
        return view('employee.attendance.index', compact('attendance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'latitude' => 'required',
            'longitude' => 'required',
            'photo' => 'required', // Base64 Image
            'type' => 'required|in:in,out', // Masuk atau Pulang
        ]);

        $user = Auth::user();
        $now = Carbon::now();

        // 1. Cek Jarak (Geofencing)
        $distance = $this->calculateDistance($this->officeLat, $this->officeLng, $request->latitude, $request->longitude);
        
        if ($distance > $this->radiusKm) {
            return response()->json([
                'status' => 'error',
                'message' => 'Anda diluar jangkauan kantor! Jarak: ' . number_format($distance * 1000) . ' meter.'
            ], 422);
        }

        // 2. Upload Foto (Decode Base64)
        $image_parts = explode(";base64,", $request->photo);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_base64 = base64_decode($image_parts[1]);
        
        $fileName = 'attendance/' . $user->id . '_' . time() . '_' . $request->type . '.jpg';
        Storage::disk('public')->put($fileName, $image_base64);

        // 3. Simpan / Update Database
        $attendance = Attendance::firstOrNew([
            'user_id' => $user->id,
            'date' => $now->toDateString(),
        ]);

        if ($request->type == 'in') {
            // LOGIC ABSEN MASUK
            if ($attendance->clock_in) {
                return response()->json(['status' => 'error', 'message' => 'Anda sudah absen masuk!']);
            }
            
            // Cek Telat
            $shift = Shift::getCurrentShift();
            $jamMasuk = Carbon::parse($shift->clock_in);
            $lateMinutes = $now->gt($jamMasuk) ? $now->diffInMinutes($jamMasuk) : 0;

            $attendance->clock_in = $now->toTimeString();
            $attendance->photo_in = $fileName;
            $attendance->location_lat = $request->latitude;
            $attendance->location_long = $request->longitude;
            $attendance->late_minutes = $lateMinutes;
            $attendance->is_late = ($lateMinutes > 0);
            $attendance->status = 'present';
            $attendance->save();

            return response()->json(['status' => 'success', 'message' => 'Absen Masuk Berhasil!']);

        } else {
            // LOGIC ABSEN PULANG
            if (!$attendance->clock_in) {
                return response()->json(['status' => 'error', 'message' => 'Belum absen masuk!']);
            }

            $attendance->clock_out = $now->toTimeString();
            $attendance->photo_out = $fileName;
            $attendance->save();

            return response()->json(['status' => 'success', 'message' => 'Absen Pulang Berhasil!']);
        }
    }

    // Rumus Jarak (Haversine)
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return ($miles * 1.609344); // Konversi ke KM
    }
}