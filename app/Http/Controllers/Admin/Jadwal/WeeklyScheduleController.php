<?php

namespace App\Http\Controllers\Admin\Jadwal;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WeeklySchedule;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class WeeklyScheduleController extends Controller
{
    /**
     * Daftar 7 hari baku (ISO-8601: 1 = Senin s/d 7 = Minggu)
     */
    private function getDefaultDays(): array
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];
    }

    /**
     * [BARU] Menampilkan halaman Pola Kerja Mingguan & auto-seed 7 hari jika tabel masih kosong
     */
    public function index()
    {
        $shifts = Shift::orderBy('id', 'asc')->get();
        $defaultDays = $this->getDefaultDays();
        $table = (new WeeklySchedule())->getTable();

        $dayColumn = Schema::hasColumn($table, 'day_number') ? 'day_number' : 'day_of_week';
        $hasDayName = Schema::hasColumn($table, 'day_name');
        $hasIsOff = Schema::hasColumn($table, 'is_off');

        // Otomatis isi 7 hari default jika database masih kosong
        if (WeeklySchedule::count() === 0) {
            $defaultWorkShift = $shifts->where('is_holiday', false)->first() ?? $shifts->first();
            $defaultHolidayShift = $shifts->where('is_holiday', true)->first();

            foreach ($defaultDays as $dayNum => $dayName) {
                $isWeekendOff = ($dayNum === 7);
                $data = [
                    'shift_id' => $isWeekendOff
                        ? ($defaultHolidayShift->id ?? null)
                        : ($defaultWorkShift->id ?? null),
                ];

                if ($hasDayName) {
                    $data['day_name'] = $dayName;
                }
                if ($hasIsOff) {
                    $data['is_off'] = $isWeekendOff;
                }

                WeeklySchedule::updateOrCreate([$dayColumn => $dayNum], $data);
            }
        }

        $schedules = WeeklySchedule::with('shift')->orderBy($dayColumn, 'asc')->get();
        $weeklyPattern = $schedules;

        return view('admin.jadwal.weekly.index', compact('schedules', 'weeklyPattern', 'shifts'));
    }

    /**
     * Alias method update() agar kompatibel dengan route resource / PUT
     */
    public function update(Request $request)
    {
        return $this->updatePola($request);
    }

    /**
     * Menyimpan perubahan Pola Kerja Mingguan & sinkronisasi ke shift karyawan
     */
    public function updatePola(Request $request)
    {
        /**
         * @var \App\Models\User $user
         */
        $user = Auth::user(); // Ini memperbaiki error 'user'

        // Perbaikan pada baris 77 (sesuai log error Anda)
        if (!$user) {
            return redirect()->back()->with('error', 'Sesi berakhir.');
        }

        // Contoh perbaikan pada baris 92 (pengambilan ID)
        // Gunakan $user->id secara langsung atau auth()->id()
        $userId = $user->id;

        $defaultDays = $this->getDefaultDays();
        $table = (new WeeklySchedule())->getTable();

        // Deteksi kolom tabel secara dinamis agar aman dari perbedaan struktur migrasi
        $dayColumn    = Schema::hasColumn($table, 'day_number') ? 'day_number' : 'day_of_week';
        $hasDayName   = Schema::hasColumn($table, 'day_name');
        $hasIsOff     = Schema::hasColumn($table, 'is_off');
        $hasUpdatedBy = Schema::hasColumn($table, 'updated_by');

        $patternsInput  = $request->input('patterns', []);
        $schedulesInput = $request->input('schedules', []);

        if (empty($patternsInput) && empty($schedulesInput)) {
            return redirect()->back()
                ->with('active_tab', 'pattern')
                ->with('error', 'Data pola mingguan tidak ditemukan. Silakan periksa kembali form Anda.');
        }

        DB::beginTransaction();

        try {
            $normalizedMap = [];

            foreach ($defaultDays as $dayNumber => $dayName) {
                $shiftId = null;
                $isOff = false;

                // Mode 1: Input dari Form Modal Master Shift (patterns[dayNumber] => shift_id)
                if (!empty($patternsInput) && array_key_exists($dayNumber, $patternsInput)) {
                    $rawShiftId = $patternsInput[$dayNumber];
                    $shiftId = !empty($rawShiftId) ? (int) $rawShiftId : null;

                    if ($shiftId) {
                        $shiftModel = Shift::find($shiftId);
                        $isOff = (bool) ($shiftModel->is_holiday ?? false);
                    } else {
                        $isOff = true;
                    }
                }
                // Mode 2: Input dari Form Halaman Pola Mingguan (schedules[dayNumber][shift_id, is_off])
                elseif (!empty($schedulesInput) && isset($schedulesInput[$dayNumber])) {
                    $row = $schedulesInput[$dayNumber];
                    $isOff = !empty($row['is_off']);
                    $shiftId = (!$isOff && !empty($row['shift_id'])) ? (int) $row['shift_id'] : null;
                    $dayName = $row['day_name'] ?? $dayName;
                } else {
                    continue;
                }

                $normalizedMap[$dayNumber] = [
                    'shift_id' => $shiftId,
                    'is_off'   => $isOff,
                ];

                $updatePayload = [
                    'shift_id' => $shiftId,
                ];

                if ($hasDayName) {
                    $updatePayload['day_name'] = $dayName;
                }

                if ($hasIsOff) {
                    $updatePayload['is_off'] = $isOff;
                }

                if ($hasUpdatedBy) {
                    $updatePayload['updated_by'] = $userId;
                }

                // Logika simpan pola...
                WeeklySchedule::updateOrCreate(
                    [$dayColumn => $dayNumber],
                    $updatePayload
                );
            }

            // [BARU] Sinkronisasi Pola Mingguan ke Data Shift Karyawan Aktif
            $syncMode = $request->input('sync_mode', 'none');
            $successMessage = 'Pola berhasil diupdate';

            if ($syncMode !== 'none' && Schema::hasColumn('users', 'shift_id')) {
                $targetShiftId = null;

                if ($syncMode === 'today') {
                    $todayNumber = Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)
                    $targetShiftId = $normalizedMap[$todayNumber]['shift_id'] ?? null;
                    $successMessage = 'Pola berhasil diupdate & jadwal shift hari ini (' . $defaultDays[$todayNumber] . ') telah diterapkan ke karyawan aktif.';
                } elseif (in_array($syncMode, ['default_work', 'primary'], true)) {
                    $workShiftIds = Shift::where('is_holiday', false)->pluck('id')->toArray();

                    foreach ($normalizedMap as $dayData) {
                        if (!$dayData['is_off'] && !empty($dayData['shift_id']) && in_array($dayData['shift_id'], $workShiftIds)) {
                            $targetShiftId = $dayData['shift_id'];
                            break;
                        }
                    }

                    $successMessage = 'Pola berhasil diupdate & shift kerja utama telah disinkronkan ke seluruh karyawan aktif.';
                }

                // Terapkan ke karyawan aktif (mengecualikan akun superadmin/admin jika ada kolom role)
                $employeeQuery = User::query();

                if (Schema::hasColumn('users', 'is_active')) {
                    $employeeQuery->where('is_active', 1);
                } elseif (Schema::hasColumn('users', 'status')) {
                    $employeeQuery->whereIn('status', ['active', 'aktif', 1]);
                }

                if (Schema::hasColumn('users', 'role')) {
                    $employeeQuery->whereNotIn('role', ['superadmin', 'admin']);
                }

                if ($syncMode === 'today' || !empty($targetShiftId)) {
                    $employeeQuery->update(['shift_id' => $targetShiftId]);
                }
            }

            DB::commit();

            // Jika request berasal dari halaman standalone Pola Mingguan, kembali ke halaman sebelumnya
            if (!empty($schedulesInput) && empty($patternsInput)) {
                return redirect()->back()->with('success', $successMessage);
            }

            return redirect()->route('admin.master.jadwal.shift.index')
                ->with('active_tab', 'pattern')
                ->with('success', $successMessage);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui pola kerja mingguan: ' . $e->getMessage(), [
                'user_id' => $userId,
                'trace'   => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('active_tab', 'pattern')
                ->with('error', 'Terjadi kesalahan saat menyimpan pola: ' . $e->getMessage());
        }
    }
}