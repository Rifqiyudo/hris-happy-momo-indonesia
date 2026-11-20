<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, DB, Log};
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\{Attendance, Leave, Payroll, User, Announcement, Branch};

class MyDashboardController extends Controller
{
    /**
     * 1. HALAMAN UTAMA DASHBOARD KARYAWAN
     * Menampilkan statistik, payroll, dan pengumuman yang relevan.
     */
    public function index()
    {
        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // A. Data Absensi Hari Ini
        $todayAttendance = Attendance::where('user_id', $user->id)
                                     ->whereDate('date', $today)
                                     ->first();

        // B. Statistik Real-Time (Cuti, Hadir, Telat)
        $quota = $user->leaveAllowance ? $user->leaveAllowance->quota : 0;
        $usedLeaveDays = Leave::where('user_id', $user->id)
                                    ->where('type', 'Cuti')
                                    ->where('status', 'approved')
                                    ->whereYear('start_date', $currentYear)
                                    ->get()
                                    ->sum(function($leave) {
                                        return Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
                                    });

        $stats = [
            'hadir'     => Attendance::where('user_id', $user->id)
                                    ->whereMonth('date', $currentMonth)
                                    ->whereYear('date', $currentYear)
                                    ->count(),
            'terlambat' => Attendance::where('user_id', $user->id)
                                    ->whereMonth('date', $currentMonth)
                                    ->whereYear('date', $currentYear)
                                    ->where('is_late', true)
                                    ->count(),
            'sisa_cuti' => max(0, $quota - $usedLeaveDays), 
        ];

        // C. Payroll & Pengajuan Terbaru
        $lastPayroll = Payroll::where('user_id', $user->id)->where('status', 'published')->latest()->first();
        $recentRequests = Leave::where('user_id', $user->id)->latest()->take(3)->get();

        // D. FITUR PENGUMUMAN (LOGIKA DIPERBAIKI)
        // Memastikan pengumuman AKTIF DAN (Global ATAU Sesuai Departemen User)
        $announcements = Announcement::where('is_active', true)
            ->where(function($query) use ($user) {
                $query->where('target', 'all')
                      ->orWhere('department_id', $user->department_id);
            })
            ->latest()
            ->get();

        return view('employee.dashboard', compact(
            'user', 'todayAttendance', 'stats', 'lastPayroll', 'recentRequests', 'announcements'
        ));
    }

    /**
     * 2. HALAMAN KAMERA ABSENSI
     */
    public function clockInPage()
    {
        $user = Auth::user();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', Carbon::today())
                                ->first();

        if ($attendance && $attendance->clock_out) {
             return redirect()->route('employee.dashboard')
                            ->with('info', 'Absensi hari ini sudah selesai. Selamat beristirahat!');
        }

        return view('employee.attendance.create', compact('attendance'));
    }

    /**
     * 3. PROSES SIMPAN ABSENSI (Geo-fencing & Face Snapshot)
     */
    public function storeVisit(Request $request)
    {
        $request->validate([
            'image'     => 'required', 
            'latitude'  => 'required',
            'longitude' => 'required',
        ]);

        $user = Auth::user();
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // VALIDASI JARAK (GEO-FENCING)
        $branch = $user->branch;
        if ($branch && $branch->latitude && $branch->longitude) {
            $distance = $this->calculateDistance(
                $request->latitude, $request->longitude, 
                $branch->latitude, $branch->longitude
            );

            $radius = $branch->radius ?? 100; // Default radius 100 meter
            if ($distance > $radius) {
                return redirect()->back()->with('error', "Anda berada di luar radius kantor ($distance meter). Jarak maksimal adalah $radius meter.");
            }
        }

        try {
            DB::beginTransaction();

            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', $today)
                                    ->first();

            // Decode Base64 Image
            $image = $request->image;
            $image = str_replace(['data:image/jpeg;base64,', 'data:image/png;base64,'], '', $image);
            $image = str_replace(' ', '+', $image);
            $folder = !$attendance ? 'in' : 'out';
            $imageName = "attendance/{$folder}/" . $user->nik . '_' . time() . '.jpg';
            
            Storage::disk('public')->put($imageName, base64_decode($image));

            if (!$attendance) {
                // Logika Jam Masuk & Toleransi Keterlambatan
                $startTime = $user->shift ? $user->shift->start_time : '08:00:00';
                $shiftStart = Carbon::parse($today . ' ' . $startTime);
                $tolerance = $user->shift ? $user->shift->late_tolerance : 0;
                
                $isLate = $now->greaterThan($shiftStart->addMinutes($tolerance));
                $lateMinutes = $isLate ? $now->diffInMinutes($shiftStart->subMinutes($tolerance)) : 0;

                Attendance::create([
                    'user_id'      => $user->id,
                    'date'         => $today,
                    'clock_in'     => $now->toTimeString(),
                    'latitude'     => $request->latitude,
                    'longitude'    => $request->longitude,
                    'address'      => $request->address ?? 'Lokasi Terdeteksi',
                    'image_in'     => $imageName,
                    'status'       => $isLate ? 'telat' : 'hadir',
                    'is_late'      => $isLate,
                    'late_minutes' => $lateMinutes,
                    'category'     => 'office'
                ]);
                $message = 'Absen masuk berhasil dicatat. Selamat bekerja!';
            } else {
                // Logika Absen Pulang (Minimal jeda 5 menit dari absen masuk)
                if ($now->diffInMinutes(Carbon::parse($attendance->clock_in)) < 5) {
                    return redirect()->back()->with('error', 'Minimal jeda 5 menit untuk melakukan absen pulang.');
                }

                $attendance->update([
                    'clock_out' => $now->toTimeString(),
                    'image_out' => $imageName,
                ]);
                $message = 'Absen pulang berhasil dicatat. Hati-hati di jalan!';
            }

            DB::commit();
            return redirect()->route('employee.dashboard')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Attendance Store Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memproses absensi. Silakan coba lagi.');
        }
    }

    /**
     * Helper: Haversine Formula untuk Hitung Jarak (Meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // Dalam satuan Meter
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        return round($earthRadius * $c);
    }
}