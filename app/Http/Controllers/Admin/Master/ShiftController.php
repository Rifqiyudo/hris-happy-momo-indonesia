<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use App\Models\ShiftSchedule; // Model Pola Kerja Mingguan
use App\Models\User; // [BARU] Model Karyawan untuk sinkronisasi shift (Opsi A)
use Carbon\Carbon; // [BARU] Deteksi hari berjalan (Senin - Minggu)
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth; // [BARU] Ambil ID admin yang memperbarui pola
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema; // [BARU] Cek kolom tabel secara dinamis

class ShiftController extends Controller
{
    /**
     * [BARU] Daftar 7 Hari Baku (ISO-8601: 1 = Senin s/d 7 = Minggu)
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
     * [BARU] Memastikan 7 hari (Senin-Minggu) tersedia & Senin-Sabtu aktif kerja jika belum dikonfigurasi
     */
    private function ensureDefaultWeeklyPattern($shifts): void
    {
        $table = (new ShiftSchedule())->getTable();
        $dayColumn = Schema::hasColumn($table, 'day_number') ? 'day_number' : 'day_of_week';
        $hasDayName = Schema::hasColumn($table, 'day_name');
        $hasIsOff = Schema::hasColumn($table, 'is_off');

        $defaultWorkShift = $shifts->where('is_holiday', false)->first() ?? $shifts->first();
        $defaultHolidayShift = $shifts->where('is_holiday', true)->first();

        $totalRows = ShiftSchedule::count();
        $assignedWorkRows = ShiftSchedule::whereNotNull('shift_id')->count();

        // Auto-seed jika baris belum lengkap 7 hari ATAU semua shift_id masih NULL padahal master shift sudah ada
        if ($totalRows < 7 || ($assignedWorkRows === 0 && $defaultWorkShift)) {
            foreach ($this->getDefaultDays() as $dayNum => $dayName) {
                $isSunday = ($dayNum === 7);
                $shiftId = $isSunday
                    ? ($defaultHolidayShift->id ?? null)
                    : ($defaultWorkShift->id ?? null);

                $payload = [
                    'shift_id' => $shiftId,
                ];

                if ($hasDayName) {
                    $payload['day_name'] = $dayName;
                }

                if ($hasIsOff) {
                    $payload['is_off'] = $isSunday || ($shiftId === null);
                }

                ShiftSchedule::updateOrCreate(
                    [$dayColumn => $dayNum],
                    $payload
                );
            }
        }
    }

    /**
     * 1. INDEX: Menampilkan Data Master Shift & Pola Kerja
     */
    public function index()
    {
        // A. Ambil data master shift (Grup 1)
        $shifts = Shift::orderBy('name', 'ASC')->get();

        // [BARU] Selaraskan otomatis 7 hari default (Senin-Sabtu Kerja, Minggu Libur) jika tabel masih kosong/NULL
        $this->ensureDefaultWeeklyPattern($shifts);

        // [BARU] Deteksi kolom hari secara dinamis agar kompatibel dengan day_number maupun day_of_week
        $scheduleTable = (new ShiftSchedule())->getTable();
        $dayColumn = Schema::hasColumn($scheduleTable, 'day_number') ? 'day_number' : 'day_of_week';

        // B. Ambil data Pola Kerja Mingguan (Grup 2)
        // Eager Loading 'shift' untuk menghemat query (N+1 Problem Fix)
        $weeklyPattern = ShiftSchedule::with('shift')
                            ->orderBy($dayColumn, 'ASC')
                            ->get();

        return view('admin.master.shift.index', compact('shifts', 'weeklyPattern'));
    }

    /**
     * 2. STORE: Menyimpan Template Shift Baru
     */
    public function store(Request $request)
    {
        // Validasi Input
        $request->validate([
            'name'       => 'required|string|max:255',
            'start_time' => 'required',
            'end_time'   => 'required',
            'color'      => 'required|string',
        ], [
            'name.required'       => 'Nama shift wajib diisi.',
            'start_time.required' => 'Jam masuk wajib diisi.',
            'end_time.required'   => 'Jam pulang wajib diisi.',
            'color.required'      => 'Warna label wajib dipilih.',
        ]);

        try {
            // Generate Kode Unik jika kosong
            $code = $request->code ?? 'SH-' . strtoupper(Str::random(4));

            // Simpan ke Database
            $shift = Shift::create([
                'name'                  => $request->name,
                'code'                  => $code,
                'start_time'            => $request->start_time,
                'end_time'              => $request->end_time,
                'break_start'           => $request->break_start,
                'break_end'             => $request->break_end,
                'late_tolerance'        => $request->late_tolerance ?? 0,
                'early_leave_tolerance' => $request->early_leave_tolerance ?? 0,
                'color'                 => $request->color ?? '#4f46e5',
                'is_night_shift'        => $request->has('is_night_shift'),
                'is_holiday'            => $request->has('is_holiday'),
                'description'           => $request->description,
            ]);

            // [BARU] Jika ini adalah shift kerja pertama dan pola mingguan belum terisi sama sekali, otomatis petakan Senin-Sabtu
            if (!$shift->is_holiday && ShiftSchedule::whereNotNull('shift_id')->count() === 0) {
                $this->ensureDefaultWeeklyPattern(Shift::orderBy('name', 'ASC')->get());
            }

            // KEMBALI KE TAB MASTER
            return redirect()->back()
                ->with('success', 'Master shift baru berhasil dibuat.')
                ->with('active_tab', 'master');

        } catch (\Exception $e) {
            Log::error("Shift Store Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Terjadi kesalahan sistem saat menyimpan data.')
                ->with('active_tab', 'master');
        }
    }

    /**
     * 3. UPDATE: Memperbarui Data Master Shift
     */
    public function update(Request $request, $id)
    {
        $shift = Shift::findOrFail($id);

        $request->validate([
            'name'       => 'required|string|max:255',
            'start_time' => 'required',
            'end_time'   => 'required',
        ]);

        try {
            $shift->update([
                'name'                  => $request->name,
                'start_time'            => $request->start_time,
                'end_time'              => $request->end_time,
                'break_start'           => $request->break_start,
                'break_end'             => $request->break_end,
                'late_tolerance'        => $request->late_tolerance ?? 0,
                'early_leave_tolerance' => $request->early_leave_tolerance ?? 0,
                'color'                 => $request->color ?? $shift->color,
                'is_night_shift'        => $request->has('is_night_shift'),
                'is_holiday'            => $request->has('is_holiday'),
                'description'           => $request->description,
            ]);

            // KEMBALI KE TAB MASTER
            return redirect()->back()
                ->with('success', 'Data shift berhasil diperbarui.')
                ->with('active_tab', 'master');

        } catch (\Exception $e) {
            Log::error("Shift Update Error: " . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data.')
                ->with('active_tab', 'master');
        }
    }

    /**
     * 4. UPDATE PATTERN: Memperbarui Pola Mingguan secara Massal
     */
    public function updatePattern(Request $request)
    {
        // Validasi: pastikan 'patterns' terkirim dalam bentuk array
        $request->validate([
            'patterns' => 'required|array',
        ]);

        try {
            // Gunakan Transaction agar jika 1 hari gagal, semua dibatalkan (keamanan data)
            DB::beginTransaction();

            // [BARU] Deteksi struktur kolom tabel shift_schedules secara dinamis
            $table = (new ShiftSchedule())->getTable();
            $dayColumn    = Schema::hasColumn($table, 'day_number') ? 'day_number' : 'day_of_week';
            $hasDayName   = Schema::hasColumn($table, 'day_name');
            $hasIsOff     = Schema::hasColumn($table, 'is_off');
            $hasUpdatedBy = Schema::hasColumn($table, 'updated_by');
            $defaultDays  = $this->getDefaultDays();
            $savedMap     = [];

            foreach ($request->patterns as $dayNumber => $shiftId) {
                $dayNum = (int) $dayNumber;

                // [BARU] Normalisasi string kosong "" menjadi null agar MySQL tidak menolak foreign key
                $normalizedShiftId = !empty($shiftId) ? (int) $shiftId : null;
                $shiftModel = $normalizedShiftId ? Shift::find($normalizedShiftId) : null;
                $isOff = !$shiftModel || (bool) ($shiftModel->is_holiday ?? false);

                $savedMap[$dayNum] = [
                    'shift_id' => $normalizedShiftId,
                    'is_off'   => $isOff,
                ];

                $payload = [
                    'shift_id' => $normalizedShiftId, // Jika dropdown pilih "Libur", value menjadi null
                ];

                if ($hasDayName && isset($defaultDays[$dayNum])) {
                    $payload['day_name'] = $defaultDays[$dayNum];
                }

                if ($hasIsOff) {
                    $payload['is_off'] = $isOff;
                }

                if ($hasUpdatedBy && Auth::check()) {
                    $payload['updated_by'] = Auth::id();
                }

                // [DIPERBARUI] Gunakan updateOrCreate agar baris tetap tersimpan meski tabel sebelumnya masih kosong
                ShiftSchedule::updateOrCreate(
                    [$dayColumn => $dayNum],
                    $payload
                );
            }

            // [BARU] Sinkronisasi Pola Mingguan ke Data Shift Karyawan Aktif (Opsi A)
            $syncMode = $request->input('sync_mode', 'none');
            $successMessage = 'Pola kerja mingguan (Senin-Minggu) berhasil diselaraskan.';

            if ($syncMode !== 'none' && Schema::hasColumn('users', 'shift_id')) {
                $targetShiftId = null;
                $todayNumber = Carbon::now()->dayOfWeekIso; // 1 (Senin) - 7 (Minggu)

                if ($syncMode === 'today') {
                    $targetShiftId = $savedMap[$todayNumber]['shift_id'] ?? null;
                    $todayLabel = $defaultDays[$todayNumber] ?? 'Hari Ini';
                    $successMessage = "Pola kerja mingguan berhasil diselaraskan & jadwal {$todayLabel} telah diterapkan ke karyawan aktif.";
                } elseif (in_array($syncMode, ['default_work', 'primary'], true)) {
                    foreach ($savedMap as $dayData) {
                        if (!$dayData['is_off'] && !empty($dayData['shift_id'])) {
                            $targetShiftId = $dayData['shift_id'];
                            break;
                        }
                    }
                    $successMessage = 'Pola kerja mingguan berhasil diselaraskan & shift kerja utama telah diterapkan ke seluruh karyawan aktif.';
                }

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
            
            // KEMBALI KE TAB PATTERN (POLA MINGGUAN)
            return redirect()->back()
                ->with('success', $successMessage)
                ->with('active_tab', 'pattern'); // <--- INI KUNCINYA

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Pattern Update Error: " . $e->getMessage());
            
            // KEMBALI KE TAB PATTERN DENGAN ERROR
            return redirect()->back()
                ->with('error', 'Gagal memperbarui pola kerja.')
                ->with('active_tab', 'pattern'); // <--- TETAP DI TAB PATTERN JIKA ERROR
        }
    }

    /**
     * 5. DESTROY: Menghapus Shift
     */
    public function destroy($id)
    {
        try {
            $shift = Shift::findOrFail($id);

            // Cek apakah shift ini sedang dipakai di pola mingguan?
            // Ini untuk mencegah error Integrity Constraint di database
            $isUsed = ShiftSchedule::where('shift_id', $id)->exists();

            if ($isUsed) {
                 return redirect()->back()
                    ->with('error', 'Gagal hapus! Shift ini masih aktif digunakan dalam Pola Kerja Mingguan. Silakan ganti pola kerja terlebih dahulu.')
                    ->with('active_tab', 'master');
            }

            $shift->delete(); 
            return redirect()->back()
                ->with('success', 'Template shift berhasil dihapus.')
                ->with('active_tab', 'master');

        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Data tidak dapat dihapus karena masih terkait dengan data absensi karyawan.')
                ->with('active_tab', 'master');
        }
    }
}