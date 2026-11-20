<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{User, Payroll, Attendance, Salary, LeaveRequest, Kpi, Allowance, Overtime}; // Ditambahkan Model Overtime
use Carbon\Carbon;
use Illuminate\Support\Facades\{DB, Log};
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now()->format('m')); 
        $year  = $request->input('year', Carbon::now()->year);

        // [FITUR BARU] Tangkap input pencarian nama / NIK karyawan
        $rawSearch = $request->input('search') ?? $request->input('nama') ?? $request->input('q');
        $search    = $rawSearch ? trim($rawSearch) : null;
        
        $query = Payroll::with(['user.position', 'user.branch', 'user.department'])
            ->where('month', $month)
            ->where('year', $year);

        // [FITUR BARU] Filter pencarian berdasarkan Nama Karyawan, NIK, Jabatan, atau Cabang
        if ($search) {
            $keywords = preg_split('/\s+/', strtolower($search));

            $query->whereHas('user', function ($q) use ($search, $keywords) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere(function ($subName) use ($keywords) {
                      foreach ($keywords as $word) {
                          $subName->whereRaw('LOWER(name) LIKE ?', ["%{$word}%"]);
                      }
                  })
                  ->orWhereHas('position', function ($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('branch', function ($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('department', function ($d) use ($search) {
                      $d->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // [FITUR BARU] Hitung statistik ringkasan sesuai hasil filter pencarian
        $statsQuery    = clone $query;
        $totalGaji     = (clone $statsQuery)->sum('net_salary');
        $totalKaryawan = (clone $statsQuery)->count();
        $totalDraft    = (clone $statsQuery)->whereIn('status', ['draft', 'Draft', 'pending', 'Pending'])->count();

        $payrolls = $query->latest()
            ->paginate(15)
            ->withQueryString();

        // [FITUR BARU] Dukungan response JSON jika dipanggil via AJAX live-search
        if ($request->ajax()) {
            return response()->json([
                'success'       => true,
                'totalGaji'     => $totalGaji,
                'totalKaryawan' => $totalKaryawan,
                'totalDraft'    => $totalDraft,
                'data'          => $payrolls->items(),
            ]);
        }

        $months = ['01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April', '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus', '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'];
        $years = range(Carbon::now()->year, Carbon::now()->year + 3);

        return view('admin.transaksi.payroll.index', compact(
            'payrolls',
            'months',
            'years',
            'month',
            'year',
            'search',
            'totalGaji',
            'totalKaryawan',
            'totalDraft'
        ));
    }

    public function create()
    {
        // ====================================================================
        // [FITUR BARU] MENGIRIM DATA KARYAWAN + PENCARIAN TUNJANGAN OTOMATIS
        // ====================================================================
        // Memuat data User yang aktif, beserta relasi Branch dan Position.
        $employees = User::with(['branch', 'position'])->where('is_active', true)->get();

        // Tarik paksa nilai Tunjangan Jabatan dari DB Allowance 
        // yang tertuju pada masing-masing Karyawan (user_id) 
        // atau jabatannya (position_id) bertipe 'fixed'.
        foreach ($employees as $emp) {
            $tjJabatanVal = Allowance::where('is_active', true)
                ->where('type', 'fixed') // Memastikan ini Tunjangan Tetap
                ->where(function ($query) use ($emp) {
                    $query->where('user_id', $emp->id) // Sesuai gambar, target ke User
                          ->orWhere('position_id', $emp->position_id) // Atau target ke Jabatan
                          ->orWhere(function($q) {
                              $q->whereNull('user_id')->whereNull('position_id'); // Atau berlaku Global
                          });
                })
                ->where('name', 'like', '%Jabatan%') // Memfilter tunjangan yang ada kata 'Jabatan'
                ->sum('amount'); // Jumlahkan atau ambil nominalnya

            // Tempelkan nilai ini sebagai properti virtual agar bisa dibaca di Blade
            $emp->tj_jabatan_otomatis = $tjJabatanVal ?? 0;
        }

        return view('admin.transaksi.payroll.create', compact('employees'));
    }

    /**
     * GENERATE MASSAL GAJI (VERSI REAL-TIME / PRODUCTION)
     * - Cut-off Gaji & Absensi: 26 Bulan Lalu s/d 25 Bulan Ini
     * - Denda Keterlambatan: Kelipatan 15 Menit = Rp 5.000
     * - Lembur Dinamis: Total Jam Lembur (Approved) x Rate Lembur
     * - BPJS: Menggunakan Nominal Murni
     */
    public function generateMass(Request $request)
    {
        $request->validate(['month' => 'required', 'year' => 'required|integer']);
        $month = $request->month;
        $year  = $request->year;

        try {
            DB::beginTransaction();

            // ====================================================================
            // 0. DEFINISI TANGGAL CUT-OFF (Tgl 26 Bulan Lalu s/d 25 Bulan Ini)
            // ====================================================================
            $startDateGaji = Carbon::createFromDate($year, $month, 26)->subMonth()->format('Y-m-d');
            $endDateGaji = Carbon::createFromDate($year, $month, 25)->format('Y-m-d');

            // Ambil semua karyawan aktif yang sudah disetting master gajinya
            $employees = User::where('is_active', true)->whereHas('salary')->with(['salary', 'position'])->get();

            foreach ($employees as $emp) {
                $salary = $emp->salary;
                $baseSalary = $salary->base_salary ?? 0;

                // ====================================================================
                // 1. PERHITUNGAN HARI KERJA & ABSENSI (BERDASARKAN CUT-OFF)
                // ====================================================================
                // Kehadiran (Hadir/Telat) di rentang cut-off
                $attendanceCount = Attendance::where('user_id', $emp->id)
                    ->whereBetween('date', [$startDateGaji, $endDateGaji])
                    ->whereIn('status', ['present', 'hadir', 'late', 'telat'])
                    ->count();
                
                // Cuti Berbayar (Paid Leave) di rentang cut-off
                $approvedLeaveDays = LeaveRequest::where('user_id', $emp->id)
                    ->where('status', 'Approved')
                    ->where('is_unpaid', false)
                    ->whereBetween('start_date', [$startDateGaji, $endDateGaji])
                    ->sum('total_days');
                
                // Total Hari Kerja Aktual yang Diakui
                $totalDaysWorked = $attendanceCount + $approvedLeaveDays;

                // ====================================================================
                // 2. LOGIKA POTONGAN ALPHA & DENDA TERLAMBAT (HAPPY MOMO LOGIC)
                // ====================================================================
                
                // Potongan Alpha / Mangkir -> DI-NOL-KAN
                $potonganMangkir = 0; 

                // Denda Keterlambatan Aktual: Tiap kelipatan 15 Menit = Rp 5.000
                $lateAttendances = Attendance::where('user_id', $emp->id)
                    ->whereBetween('date', [$startDateGaji, $endDateGaji])
                    ->where('is_late', true)
                    ->where('late_minutes', '>', 0)
                    ->get();

                $lateDeduction = 0;
                foreach ($lateAttendances as $lateAtt) {
                    $faktorDenda = ceil($lateAtt->late_minutes / 15);
                    $lateDeduction += ($faktorDenda * 5000);
                }

                // ====================================================================
                // 3. KALKULASI LEMBUR DINAMIS (OVERTIME)
                // ====================================================================
                $overtimes = Overtime::where('user_id', $emp->id)
                    ->whereBetween('date', [$startDateGaji, $endDateGaji])
                    ->where('status', 'approved')
                    ->get();

                $totalOvertimeHours = $overtimes->sum('duration_hours');
                // Mengambil tarif lembur dari master salary atau set default rate (misal 20000) jika tidak ada
                $overtimeRate = $salary->overtime_amount ?? 20000; 
                $calculatedOvertimePay = $totalOvertimeHours * $overtimeRate;

                // ====================================================================
                // 4. KPI BONUS & TARIK TUNJANGAN DINAMIS (DIMATIKAN SEMENTARA)
                // ====================================================================
                $kpiBonus = 0;
                $dynamicFixedAllowance = 0;
                $dynamicDailyAllowance = 0;

                // ====================================================================
                // 5. KALKULASI PENDAPATAN (MURNI DARI MASTER GAJI + REALTIME DATA)
                // ====================================================================
                $positionAllowance = $salary->position_allowance ?? 0;
                $familyAllowance   = $salary->family_allowance ?? 0;
                $mealPerDay        = $salary->meal_allowance ?? 0;
                $transportPerDay   = $salary->transport_allowance ?? 0;
                $thrAmount         = $salary->thr_amount ?? 0;
                $bonusAmount       = $salary->bonus_amount ?? 0;
                $otherAllowance    = $salary->other_allowance ?? 0;

                $mealTotal      = $mealPerDay * $totalDaysWorked;
                $transportTotal = $transportPerDay * $totalDaysWorked;

                // Total Semua Tunjangan (memasukkan hasil kalkulasi lembur aktual)
                $totalAllowance = $positionAllowance + $familyAllowance + $mealTotal + $transportTotal + $thrAmount + $bonusAmount + $calculatedOvertimePay + $otherAllowance + $kpiBonus + $dynamicFixedAllowance + $dynamicDailyAllowance;

                // ====================================================================
                // 6. KALKULASI TOTAL POTONGAN & GAJI BERSIH (NOMINAL BPJS MURNI)
                // ====================================================================
                $jhtDeduction = $salary->jht_deduction ?? 0;
                $kesDeduction = $salary->kes_deduction ?? 0;
                $jpDeduction  = $salary->jp_deduction ?? 0; 
                
                // Potongan manual dari HRD (Kasbon, denda Izin/Alpha, dll)
                $routineDeduction = $salary->routine_deduction ?? 0;

                $totalDeduction = $jhtDeduction + $kesDeduction + $jpDeduction + $routineDeduction + $lateDeduction + $potonganMangkir;
                $netSalary = ($baseSalary + $totalAllowance) - $totalDeduction;

                // ====================================================================
                // 7. SIMPAN KE DATABASE PAYROLL
                // ====================================================================
                Payroll::updateOrCreate(
                    ['user_id' => $emp->id, 'month' => $month, 'year' => $year],
                    [
                        // Data Potongan Waktu & Gaji Pokok
                        'period_start'                => $startDateGaji,
                        'period_end'                  => $endDateGaji,
                        'base_salary'                 => $baseSalary,
                        'position_allowance'          => $positionAllowance,
                        'family_allowance'            => $familyAllowance,
                        'meal_allowance_per_day'      => $mealPerDay,
                        'meal_allowance_total'        => $mealTotal,
                        'transport_allowance_per_day' => $transportPerDay,
                        'transport_allowance_total'   => $transportTotal,
                        'thr_amount'                  => $thrAmount,
                        'bonus_amount'                => $bonusAmount, 
                        'overtime_amount'             => $calculatedOvertimePay, // Menyimpan hasil kali jam x rate aktual
                        'other_allowance'             => $otherAllowance + $dynamicFixedAllowance + $dynamicDailyAllowance + $kpiBonus, 
                        
                        // Nilai Snapshot Persen diset 0 karena menggunakan Nominal
                        'jht_percent_snapshot'        => 0,
                        'kes_percent_snapshot'        => 0,
                        'jp_percent_snapshot'         => 0,
                        
                        // Potongan Nominal
                        'jht_deduction'               => $jhtDeduction,
                        'kes_deduction'               => $kesDeduction,
                        'jp_deduction'                => $jpDeduction,
                        'routine_deduction'           => $routineDeduction,
                        'late_deduction'              => $lateDeduction,
                        'unpaid_deduction_amount'     => $potonganMangkir, 
                        
                        // Agregat Total
                        'total_days_worked'           => $totalDaysWorked,
                        'total_leave'                 => $approvedLeaveDays,
                        'total_allowance'             => $totalAllowance,
                        'total_deduction'             => $totalDeduction,
                        'net_salary'                  => max(0, $netSalary),
                        
                        // Status & Metadata
                        'status'                      => 'draft',
                        'generated_date'              => now(),
                        'updated_at'                  => now()
                    ]
                );
            }

            DB::commit();
            return redirect()->route('admin.transaksi.payroll.index', ['month' => $month, 'year' => $year])
                ->with('success', "Payroll periode $month/$year berhasil diproses secara akurat.");
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Payroll Generate Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function show($id) 
    { 
        return view('admin.transaksi.payroll.show', [
            'payroll' => Payroll::with(['user.position', 'user.branch', 'user.department'])->findOrFail($id)
        ]); 
    }
    
    public function publish($id) 
    { 
        Payroll::findOrFail($id)->update(['status' => 'paid', 'payment_date' => now()]); 
        return back()->with('success', 'Payroll berhasil dibayar.'); 
    }
    
    public function printSlip($id) 
    { 
        $payroll = Payroll::with(['user.position', 'user.branch', 'user.department'])->findOrFail($id); 
        
        return Pdf::loadView('admin.report.pdf.slip-gaji-pdf', [
            'payroll' => $payroll, 
            'company' => 'PT. Happy Momo Indonesia' // Memastikan parameter terkirim ke PDF
        ])->stream('Slip-Gaji-'.$payroll->user->name.'.pdf'); 
    }
    
    public function destroy($id) 
    { 
        $payroll = Payroll::findOrFail($id); 
        if ($payroll->status == 'paid') return back()->with('error', 'Tidak dapat menghapus data yang sudah dibayar.'); 
        
        $payroll->delete(); 
        return back()->with('success', 'Data berhasil dihapus.'); 
    }

    /**
     * [FITUR BARU] AJAX LIVE SEARCH PAYROLL (NAMA / NIK)
     */
    public function searchAjax(Request $request)
    {
        $month   = $request->input('month', Carbon::now()->format('m'));
        $year    = $request->input('year', Carbon::now()->year);
        $keyword = trim($request->input('search', $request->input('q', '')));

        $query = Payroll::with(['user.position', 'user.branch', 'user.department'])
            ->where('month', $month)
            ->where('year', $year);

        if ($keyword !== '') {
            $query->whereHas('user', function ($q) use ($keyword) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($keyword) . '%'])
                  ->orWhere('nik', 'like', "%{$keyword}%");
            });
        }

        $results = $query->latest()->limit(30)->get();

        return response()->json([
            'success'       => true,
            'totalGaji'     => $results->sum('net_salary'),
            'totalKaryawan' => $results->count(),
            'totalDraft'    => $results->whereIn('status', ['draft', 'Draft', 'pending', 'Pending'])->count(),
            'data'          => $results,
        ]);
    }
}