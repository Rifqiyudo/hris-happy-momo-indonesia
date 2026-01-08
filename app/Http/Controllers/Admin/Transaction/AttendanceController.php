<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\User;
use App\Models\Shift;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Menampilkan Data Absensi dengan Filter
     */
    public function index(Request $request)
    {
        // 1. Setup Tanggal Default (Hari ini jika tidak ada filter)
        $startDate = $request->input('start_date', Carbon::today()->toDateString());
        $endDate = $request->input('end_date', Carbon::today()->toDateString());
        $employeeId = $request->input('user_id');

        // 2. Query Builder
        $query = Attendance::with(['user.position'])
            ->whereBetween('date', [$startDate, $endDate]);

        // Jika filter karyawan dipilih
        if ($employeeId) {
            $query->where('user_id', $employeeId);
        }

        // Urutkan: Tanggal terbaru, lalu Jam Masuk terbaru
        $attendances = $query->orderBy('date', 'desc')
                             ->orderBy('clock_in', 'desc')
                             ->get();

        // Ambil daftar karyawan untuk dropdown filter
        $employees = User::where('role', 'employee')->orderBy('name')->get();

        return view('transaksi.attendance.index', compact('attendances', 'employees', 'startDate', 'endDate'));
    }

    /**
     * Menampilkan Form Edit (Untuk Koreksi Absen Manual)
     */
    public function edit($id)
    {
        $attendance = Attendance::with('user')->findOrFail($id);
        return view('transaksi.attendance.edit', compact('attendance'));
    }

    /**
     * Update Data Absensi (Logic Hitung Ulang Keterlambatan)
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $request->validate([
            'clock_in' => 'required',
            'clock_out' => 'nullable',
            'status' => 'required',
        ]);

        // 1. Ambil Jam Inputan Admin
        $jamMasukBaru = Carbon::parse($request->clock_in);
        
        // 2. Ambil Aturan Shift pada hari tersebut (Senin=1, dst)
        $dayNumber = Carbon::parse($attendance->date)->dayOfWeekIso;
        $shift = Shift::where('day_number', $dayNumber)->first();

        // 3. Hitung Ulang Keterlambatan
        $lateMinutes = 0;
        $isLate = false;

        if ($shift) {
            $jamMasukShift = Carbon::parse($shift->clock_in);
            
            // Jika jam masuk baru > jam shift
            if ($jamMasukBaru->gt($jamMasukShift)) {
                $lateMinutes = $jamMasukBaru->diffInMinutes($jamMasukShift);
                $isLate = true;
            }
        }

        // 4. Update Database
        $attendance->update([
            'clock_in' => $request->clock_in,
            'clock_out' => $request->clock_out,
            'status' => $request->status, // Hadir, Izin, Sakit
            'late_minutes' => $lateMinutes, // Update otomatis
            'is_late' => $isLate,
            'notes' => $request->notes // Alasan koreksi admin
        ]);

        return redirect()->route('transaksi.attendance.index')->with('success', 'Data absensi berhasil dikoreksi.');
    }

    /**
     * Hapus Data Absensi (Misal data tes atau duplikat)
     */
    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        // Hapus file foto fisik jika ada (Opsional, agar hemat storage)
        if($attendance->photo_in && file_exists(public_path('storage/'.$attendance->photo_in))){
            unlink(public_path('storage/'.$attendance->photo_in));
        }

        $attendance->delete();
        return redirect()->back()->with('success', 'Data absensi dihapus.');
    }
}