<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MyAttendanceController extends Controller
{
    // --- KONFIGURASI FALLBACK (Jaring Pengaman) ---
    // Digunakan jika cabang user belum diset koordinatnya
    protected $defaultLat = -6.200000;
    protected $defaultLng = 106.816666;
    protected $jamMasukDefault = '08:00:00';

    /**
     * 1. Menampilkan halaman absensi (Face Visit & Maps)
     */
    public function faceVisit()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        
        // MENGGUNAKAN branchDetail: Sesuai dengan perbaikan relasi di User.php 
        // untuk mencegah Naming Collision dengan kolom string 'branch'
        $user->load(['branchDetail', 'shift']);
        
        $dateToday = Carbon::now()->toDateString();
        
        // Mengecek apakah user sudah absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('date', $dateToday)
                                ->first();
        
        $isCheckedIn = $attendance ? true : false;
        $jamMasukKantor = $user->shift->start_time ?? $this->jamMasukDefault;

        // Data cabang untuk referensi Maps (Marker) di Frontend
        $branches = Branch::where('is_active', true)->get();

        return view('employee.attendance.face-visit', compact(
            'attendance', 
            'isCheckedIn', 
            'jamMasukKantor', 
            'user', 
            'branches'
        ));
    }

    public function create()
    {
        return $this->faceVisit();
    }

    /**
     * 2. Memproses Simpan Absensi (Clock-In & Clock-Out)
     * Mendukung mode 'office' (Geofencing) dan 'visit' (Luar Kantor/Kunjungan)
     */
    public function storeVisit(Request $request)
    {
        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
            'image'     => 'required|string', 
            'address'   => 'nullable|string',
            'category'  => 'required|in:office,visit',
        ]);

        DB::beginTransaction();

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            // Load relasi yang aman
            $user->load(['branchDetail', 'shift']);

            $now = Carbon::now();
            $dateToday = $now->toDateString();
            $timeNow = $now->toTimeString();

            // Mendapatkan data cabang dengan aman (Fallback jika null)
            $userBranch = $user->branchDetail;

            // --- A. LOGIKA GEOFENCING (Hanya berlaku jika kategori 'office') ---
            if ($request->category === 'office') {
                $officeLat = $userBranch->latitude ?? $this->defaultLat;
                $officeLng = $userBranch->longitude ?? $this->defaultLng;
                $allowedRadiusMeters = $userBranch->radius ?? 100; // Default toleransi 100 meter
                
                // Kalkulasi jarak
                $distanceKm = $this->calculateDistance($officeLat, $officeLng, $request->latitude, $request->longitude);
                $distanceMeters = round($distanceKm * 1000);

                if ($distanceMeters > $allowedRadiusMeters) {
                    return response()->json([
                        'success' => false,
                        'message' => "Di luar Radius! Jarak Anda {$distanceMeters}m dari titik pusat kantor."
                    ], 422);
                }
            }

            // --- B. PROSES WATERMARK GAMBAR ---
            // Tentukan lokasi yang akan dicetak di atas foto
            $locationTag = $request->category === 'visit' 
                ? ($request->address ?? 'LOKASI KUNJUNGAN') 
                : ($userBranch->name ?? 'KANTOR PUSAT');
            
            $fileInfo = $this->processImageWithWatermark(
                $request->image, 
                $user, 
                $now->format('d/m/Y H:i:s'), 
                $locationTag,
                $request->latitude . ', ' . $request->longitude
            );

            // --- C. DATABASE OPERATION (Simpan ke tabel attendances) ---
            $attendance = Attendance::where('user_id', $user->id)
                                    ->whereDate('date', $dateToday)
                                    ->first();

            if ($attendance) {
                // LOGIKA: CLOCK OUT (ABSEN PULANG)
                if ($attendance->clock_out) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Anda sudah melakukan absen pulang hari ini.'
                    ], 422);
                }

                $attendance->update([
                    'clock_out'   => $timeNow,
                    'image_out'   => $fileInfo['fileName'],
                    'lat_out'     => $request->latitude,
                    'lng_out'     => $request->longitude,
                    'address_out' => $request->address ?? $attendance->address,
                ]);

                $message = 'Absen Pulang Berhasil Dicatat!';
            } else {
                // LOGIKA: CLOCK IN (ABSEN MASUK)
                $shiftIn = $user->shift->start_time ?? $this->jamMasukDefault;
                $limitIn = Carbon::parse($dateToday . ' ' . $shiftIn);
                
                // Hitung keterlambatan (Hanya dihitung telat jika absen 'office')
                $lateMinutes = 0;
                if ($request->category === 'office') {
                    $lateMinutes = $now->gt($limitIn) ? $now->diffInMinutes($limitIn) : 0;
                }

                Attendance::create([
                    'user_id'       => $user->id,
                    'branch_id'     => $user->branch_id, // Tetap gunakan ID mentah untuk relasi database
                    'nik'           => $user->nik,
                    'date'          => $dateToday,
                    'clock_in'      => $timeNow,
                    'image_in'      => $fileInfo['fileName'],
                    'latitude'      => $request->latitude,
                    'longitude'     => $request->longitude,
                    'address'       => $request->address,
                    'category'      => $request->category,
                    'status'        => $lateMinutes > 0 ? 'telat' : 'hadir',
                    'is_late'       => $lateMinutes > 0,
                    'late_minutes'  => $lateMinutes,
                ]);

                $message = 'Absen Masuk Berhasil! Selamat Bekerja.';
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Absensi Error [User ID: ".Auth::id()."]: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Sistem gagal mencatat absen: Terjadi kesalahan pada server.'
            ], 500);
        }
    }

    /**
     * 3. PROSES IMAGE (WATERMARK & STORAGE)
     * Mengamankan bukti absensi dengan mencetak data digital permanen di atas foto
     */
    private function processImageWithWatermark($base64Image, $user, $timestamp, $locationName, $coords)
    {
        try {
            // Pembersihan Header Base64 bawaan HTML Canvas/Webcam
            if (str_contains($base64Image, ',')) {
                $base64Image = explode(',', $base64Image)[1];
            }

            $image_base64 = base64_decode($base64Image);
            $im = imagecreatefromstring($image_base64);
            
            if (!$im) throw new \Exception("Format gambar tidak valid atau ekstensi GD PHP tidak aktif.");

            $width = imagesx($im);
            $height = imagesy($im);

            // Membuat Kotak Background Watermark (Hitam Transparan)
            $black_overlay = imagecolorallocatealpha($im, 0, 0, 0, 60);
            imagefilledrectangle($im, 0, $height - 140, $width, $height, $black_overlay);

            // Definisi Warna Text
            $white = imagecolorallocate($im, 255, 255, 255);
            $yellow = imagecolorallocate($im, 255, 255, 0);

            // Menulis Data ke Gambar (Menggunakan Font Built-in PHP GD)
            // Parameter: imagestring(resource, font_size, x, y, text, color)
            imagestring($im, 5, 20, $height - 125, strtoupper($user->name) . " [" . $user->nik . "]", $yellow);
            imagestring($im, 4, 20, $height - 100, "WAKTU : " . $timestamp, $white);
            imagestring($im, 3, 20, $height - 75, "GPS   : " . $coords, $white);
            
            // Auto-wrap alamat agar teks tidak melampaui lebar foto
            $wrappedLoc = wordwrap($locationName, 55, "\n", true);
            $locLines = explode("\n", $wrappedLoc);
            imagestring($im, 2, 20, $height - 50, "LOC   : " . ($locLines[0] ?? ''), $white);
            
            if (isset($locLines[1])) {
                imagestring($im, 2, 20, $height - 35, "        " . $locLines[1], $white);
            }
            
            // Simpan Gambar ke Buffer untuk dikonversi ke JPEG
            ob_start();
            imagejpeg($im, null, 75); // Kualitas 75% untuk optimasi ukuran storage
            $image_data = ob_get_contents();
            ob_end_clean();
            
            // Bebaskan memory server
            imagedestroy($im);

            // Struktur Folder Berdasarkan Tanggal: public/attendances/YYYY/MM/DD/file.jpg
            $folder = 'attendances/' . date('Y/m/d');
            $fileName = $folder . '/' . $user->nik . '_' . Str::random(10) . '.jpg';
            
            // Simpan ke direktori storage public Laravel
            Storage::disk('public')->put($fileName, $image_data);

            return ['fileName' => $fileName];
            
        } catch (\Exception $e) {
            throw new \Exception("Gagal mengolah foto: " . $e->getMessage());
        }
    }

    /**
     * 4. HAVERSINE FORMULA (Hitung Jarak GPS Akurat)
     * Mengkalkulasi lengkungan bumi untuk mengukur jarak pasti antara 2 titik koordinat
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Jari-jari bumi dalam Kilometer
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
             
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c; // Mengembalikan hasil dalam Kilometer
    }
}