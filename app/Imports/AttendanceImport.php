<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AttendanceImport implements ToCollection
{
    protected $startDate;
    protected $endDate;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->startDate = $startDate ?? date('Y-m-26', strtotime('-1 month'));
        $this->endDate = $endDate ?? date('Y-m-25');
    }

    public function collection(Collection $rows)
    {
        $startObj = Carbon::parse($this->startDate);
        $endObj = Carbon::parse($this->endDate);

        $headerDays = [];
        $currentUserId = null;

        foreach ($rows as $index => $row) {
            $rowArray = $row->toArray();
            $rowString = implode(' ', $rowArray);

            // 1. Deteksi Baris Header Tanggal (Angka 26, 27... atau 1, 2...)
            foreach ($rowArray as $colIdx => $cellVal) {
                if (in_array(trim((string)$cellVal), ['26', 26, '1', 1])) {
                    foreach ($rowArray as $cIdx => $cVal) {
                        if (is_numeric(trim((string)$cVal))) {
                            $headerDays[$cIdx] = (int)trim((string)$cVal);
                        }
                    }
                    break;
                }
            }

            // 2. Deteksi Identitas Karyawan (ID & Nama secara Persis dari Baris Mesin Fingerprint)
            $rawId = null;
            $rawName = null;

            foreach ($rowArray as $colIdx => $cellVal) {
                $valStr = trim((string)$cellVal);
                
                // Jika ketemu sel 'ID:' atau 'ID'
                if (strcasecmp($valStr, 'ID:') === 0 || strcasecmp($valStr, 'ID') === 0) {
                    for ($c = $colIdx + 1; $c < count($rowArray); $c++) {
                        if (!empty($rowArray[$c]) && trim((string)$rowArray[$c]) !== 'ID:') {
                            $rawId = trim((string)$rowArray[$c]);
                            break;
                        }
                    }
                }
                
                // Jika ketemu sel 'Nama:' atau 'NAMA'
                if (strcasecmp($valStr, 'Nama:') === 0 || strcasecmp($valStr, 'NAMA') === 0 || strcasecmp($valStr, 'Nama') === 0) {
                    for ($c = $colIdx + 1; $c < count($rowArray); $c++) {
                        if (!empty($rowArray[$c]) && trim((string)$rowArray[$c]) !== 'Nama:') {
                            $rawName = trim((string)$rowArray[$c]);
                            break;
                        }
                    }
                }
            }

            // Jika ID ditemukan, pastikan Karyawan terdaftar di database secara mutlak (Auto-Create atau Update)
            if ($rawId) {
                $cleanName = $rawName ? ucwords(strtolower(str_replace('_', ' ', $rawName))) : 'Karyawan FP ' . $rawId;
                $nikToUse = 'FP-' . $rawId;

                // Cari user berdasarkan NIK atau ID angka
                $user = User::where('nik', $nikToUse)
                            ->orWhere('nik', $rawId)
                            ->orWhere('id', $rawId)
                            ->first();

                // =========================================================================
                // [FITUR BARU] PROTEKSI KARYAWAN RESIGN
                // Jika karyawan ditemukan di database tapi statusnya sudah tidak aktif (is_active = false),
                // maka abaikan baris ini agar data absensi orang yang sudah resign tidak ter-import.
                // =========================================================================
                if ($user && !$user->is_active) {
                    $currentUserId = null; // Batalkan sesi user aktif untuk baris ini
                    continue;
                }

                if (!$user) {
                    // Buat user baru dengan email unik dan status aktif
                    try {
                        $user = User::create([
                            'name'        => $cleanName,
                            'nik'         => $nikToUse,
                            'email'       => 'fp_' . $rawId . '_' . Str::random(5) . '@happymomo.local',
                            'password'    => Hash::make('password123'),
                            'role'        => 'employee',
                            'is_active'   => true,
                            'join_date'   => now(),
                        ]);
                    } catch (\Exception $e) {
                        // Fallback aman jika terjadi duplikasi
                        $user = User::firstOrCreate(
                            ['nik' => $nikToUse],
                            [
                                'name'      => $cleanName,
                                'email'     => 'fp_fallback_' . $rawId . '_' . uniqid() . '@happymomo.local',
                                'password'  => Hash::make('password123'),
                                'role'      => 'employee',
                                'is_active' => true,
                                'join_date' => now(),
                            ]
                        );

                        if ($user && !$user->is_active) {
                            $currentUserId = null;
                            continue;
                        }
                    }
                } else {
                    // Pastikan user aktif dan namanya sinkron
                    $updateData = ['is_active' => true];
                    if ($rawName && empty($user->name)) {
                        $updateData['name'] = $cleanName;
                    }
                    $user->update($updateData);
                }

                if ($user) {
                    $currentUserId = $user->id;
                }
            }

            // 3. Jika baris memuat data jam absensi (tanda ':') dan User ID sudah aktif
            if ($currentUserId && strpos($rowString, ':') !== false) {
                foreach ($rowArray as $colIdx => $cellVal) {
                    if (empty($cellVal) || $cellVal === '-' || $cellVal === '') continue;

                    $dayNum = null;
                    if (isset($headerDays[$colIdx])) {
                        $dayNum = $headerDays[$colIdx];
                    }

                    if (!$dayNum || !is_numeric($dayNum)) continue;

                    // Tentukan tanggal akurat lintas bulan (SOP: 26-31 bulan lalu, 1-25 bulan berjalan)
                    $targetDate = $startObj->copy();
                    if ($dayNum >= $startObj->day) {
                        $targetDate->setDate($startObj->year, $startObj->month, $dayNum);
                    } else {
                        $targetDate->setDate($endObj->year, $endObj->month, $dayNum);
                    }
                    $dateStr = $targetDate->format('Y-m-d');

                    // Ekstrak SEMUA jam masuk & keluar meskipun formatnya bergandengan / mepet
                    $cleanCellVal = trim((string)$cellVal);
                    $clockIn = null;
                    $clockOut = null;

                    // Menggunakan preg_match_all untuk membedah string mepet menjadi array waktu
                    if (preg_match_all('/([01]\d|2[0-3]):([0-5]\d)/', $cleanCellVal, $matches)) {
                        $times = $matches[0];
                        
                        // --- [ LOGIKA CERDAS: EVALUASI JAM ] ---
                        if (count($times) === 1) {
                            // Skenario 1: Karyawan hanya tap absensi 1 KALI saja di hari itu
                            $singleTime = $times[0];
                            $hour = (int)substr($singleTime, 0, 2);
                            
                            // Jika tap absensi terjadi pada pukul 13:00 (jam 1 siang) ke atas, 
                            // maka anggap karyawan tersebut LUPA ABSEN PAGI. Masukkan data ke kolom OUT.
                            if ($hour >= 13) {
                                $clockOut = $singleTime;
                            } else {
                                // Jika tap di bawah jam 13:00, maka masuk ke kolom IN (Pagi)
                                $clockIn = $singleTime;
                            }
                        } else if (count($times) > 1) {
                            // Skenario 2: NORMAL. Karyawan tap absensi lebih dari 1 KALI
                            $clockIn = $times[0]; // Set Jam Masuk (Selalu ambil waktu yang paling awal terdeteksi)
                            $lastTime = end($times); // Set Jam Keluar (Selalu ambil waktu yang paling ujung terdeteksi)
                            
                            // Validasi: Jika absen ujung sama persis dengan absen awal (contoh tap 2x pagi-pagi), jangan dianggap jam pulang
                            if ($clockIn !== $lastTime) {
                                $clockOut = $lastTime;
                            }
                        }
                    } else {
                        // Fallback logika lama jika tidak pakai format HH:MM
                        $clockIn = substr($cleanCellVal, 0, 5);
                    }

                    // Panggil fungsi penyimpanan (Simpan jika salah satu IN atau OUT memiliki nilai)
                    if (($clockIn && preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $clockIn)) || $clockOut) {
                        $this->saveAttendanceRecord($currentUserId, $dateStr, $cleanCellVal, $clockIn, $clockOut);
                    }
                }
            }
        }
    }

    /**
     * Helper Menyimpan / Update Absensi & Evaluasi Keterlambatan
     */
    private function saveAttendanceRecord($userId, $dateStr, $rawVal, $clockIn, $clockOut)
    {
        $status = 'Hadir';
        $isLate = false;
        $lateMinutes = 0;

        $kode = strtoupper(trim($rawVal));
        if (in_array($kode, ['C', 'I', 'S', 'A'])) {
            $statusMap = [
                'C' => 'Cuti',
                'I' => 'Izin',
                'S' => 'Sakit',
                'A' => 'Alpha/Mangkir'
            ];
            $status = $statusMap[$kode];
            $clockIn = null;
            $clockOut = null;
        } else {
            try {
                $user = User::with('shift')->find($userId);
                $shiftIn = $user->shift->start_time ?? '08:00:00';
                
                // Evaluasi telat hanya jika karyawan melakukan absen masuk (clockIn tidak null)
                if ($clockIn) {
                    $limit = Carbon::parse($dateStr . ' ' . $shiftIn);
                    $actualIn = Carbon::parse($dateStr . ' ' . $clockIn);

                    $isLate = $actualIn->gt($limit);
                    $lateMinutes = $isLate ? $actualIn->diffInMinutes($limit) : 0;
                    $status = $isLate ? 'telat' : 'hadir';
                }
            } catch (\Exception $e) {
                // Abaikan jika shift tidak ditemukan
            }
        }

        // Menyimpan jam Masuk dan jam Keluar ke Database
        Attendance::updateOrCreate(
            [
                'user_id' => $userId,
                'date'    => $dateStr
            ],
            [
                'clock_in'     => $clockIn,
                'clock_out'    => $clockOut,
                'status'       => $status,
                'is_late'      => $isLate,
                'late_minutes' => $lateMinutes,
                'category'     => 'office'
            ]
        );
    }
}