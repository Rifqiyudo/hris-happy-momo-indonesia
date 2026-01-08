<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Payroll;
use App\Models\PayrollDetail;
use App\Models\Allowance;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayrollController extends Controller
{
    // Halaman List Gaji per Bulan
    public function index(Request $request)
    {
        $month = $request->input('month', date('m'));
        $year = $request->input('year', date('Y'));

        $payrolls = Payroll::with('user.position')
                    ->where('month', $month)
                    ->where('year', $year)
                    ->latest()
                    ->get();

        return view('admin.transaksi.payroll.index', compact('payrolls', 'month', 'year'));
    }

    // Form Generate (Pilih Bulan)
    public function create()
    {
        return view('admin.transaksi.payroll.create');
    }

    // PROSES HITUNG GAJI (THE CORE LOGIC)
    public function store(Request $request)
    {
        $request->validate([
            'month' => 'required',
            'year' => 'required',
        ]);

        $month = $request->month; // 01, 02, ...
        $year = $request->year;   // 2025

        // Cek apakah bulan ini sudah digenerate?
        $cek = Payroll::where('month', $month)->where('year', $year)->exists();
        if($cek) {
            return back()->with('error', 'Gaji periode ini sudah pernah dibuat! Hapus dulu jika ingin ulang.');
        }

        // Ambil semua karyawan aktif
        $employees = User::with('position')->where('role', 'employee')->where('is_active', true)->get();
        $allowances = Allowance::all(); // Ambil Master Tunjangan

        DB::beginTransaction(); // Agar kalau error, database tidak berantakan
        try {
            foreach ($employees as $emp) {
                
                // 1. Hitung Kehadiran Bulan Ini
                $attendances = Attendance::where('user_id', $emp->id)
                                ->whereMonth('date', $month)
                                ->whereYear('date', $year)
                                ->get();
                
                $totalHadir = $attendances->count();
                $totalTelatMenit = $attendances->sum('late_minutes');

                // 2. Variable Penampung Uang
                $gajiPokok = $emp->position ? $emp->position->base_salary : 0;
                // Jika user punya gaji spesifik (override), pakai itu. Jika tidak, pakai gaji jabatan.
                if($emp->base_salary > 0) $gajiPokok = $emp->base_salary;

                $totalTunjangan = 0;
                $totalPotongan = 0;

                // 3. Buat Header Payroll
                $payroll = Payroll::create([
                    'user_id' => $emp->id,
                    'month' => $month,
                    'year' => $year,
                    'generated_date' => now(),
                    'total_days_worked' => $totalHadir,
                    'total_late_minutes' => $totalTelatMenit,
                    'base_salary' => $gajiPokok,
                    'total_allowance' => 0, // Diupdate nanti
                    'total_deduction' => 0, // Diupdate nanti
                    'net_salary' => 0,      // Diupdate nanti
                    'status' => 'draft',
                ]);

                // 4. Hitung Komponen Tunjangan (Looping Master Tunjangan)
                foreach ($allowances as $all) {
                    $nominal = 0;
                    
                    if ($all->type == 'daily') {
                        // Tunjangan Harian (Misal: Makan 25rb x 20 hari)
                        $nominal = $all->amount * $totalHadir;
                        $deskripsi = $all->name . " ({$totalHadir} hari)";
                    } else {
                        // Tunjangan Bulanan (Flat)
                        $nominal = $all->amount;
                        $deskripsi = $all->name . " (Tetap)";
                    }

                    if ($nominal > 0) {
                        PayrollDetail::create([
                            'payroll_id' => $payroll->id,
                            'name' => $deskripsi,
                            'amount' => $nominal,
                            'type' => 'allowance'
                        ]);
                        $totalTunjangan += $nominal;
                    }
                }

                // 5. Hitung Potongan Telat (Contoh: 1.000 rupiah per menit)
                // Anda bisa ubah logika ini sesuai kebijakan PT Happy Momo
                if ($totalTelatMenit > 0) {
                    $dendaTelat = $totalTelatMenit * 1000; 
                    PayrollDetail::create([
                        'payroll_id' => $payroll->id,
                        'name' => "Potongan Terlambat ({$totalTelatMenit} menit)",
                        'amount' => $dendaTelat,
                        'type' => 'deduction'
                    ]);
                    $totalPotongan += $dendaTelat;
                }

                // 6. Update Total Akhir ke Header Payroll
                $gajiBersih = ($gajiPokok + $totalTunjangan) - $totalPotongan;
                
                $payroll->update([
                    'total_allowance' => $totalTunjangan,
                    'total_deduction' => $totalPotongan,
                    'net_salary' => $gajiBersih
                ]);
            }

            DB::commit();
            return redirect()->route('admin.transaksi.payroll.index')->with('success', 'Gaji bulan ini berhasil digenerate!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    // Fitur Publish (Ubah status Draft -> Paid) agar muncul di HP Karyawan
    public function publish($id)
    {
        $payroll = Payroll::findOrFail($id);
        $payroll->update(['status' => 'paid']);
        return back()->with('success', 'Slip gaji dikirim ke karyawan.');
    }
}