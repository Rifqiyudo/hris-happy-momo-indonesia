<?php

namespace App\Http\Controllers\Admin\Transaksi;

use App\Http\Controllers\Controller;
use App\Models\Kpi;
use App\Models\KpiIndicator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KpiController extends Controller
{
    /**
     * 1. INDEX: Monitoring Daftar Nilai KPI
     */
    public function index(Request $request)
    {
        // Default ke bulan dan tahun saat ini jika tidak ada filter
        $month = $request->input('month', Carbon::now()->format('m'));
        $year = $request->input('year', Carbon::now()->year);

        $kpis = Kpi::with(['user.position', 'user.department', 'reviewer'])
            ->where('period_month', $month)
            ->where('period_year', $year)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.transaksi.kpi.index', compact('kpis', 'month', 'year'));
    }

    /**
     * 2. CREATE: Form Input Penilaian Baru
     */
    public function create()
    {
        $employees = User::where('is_active', true)
            ->where('role', 'employee')
            ->orderBy('name', 'asc')
            ->get();

        $indicators = KpiIndicator::where('is_active', true)->get();
        
        // Proteksi: Mencegah penilaian jika total bobot master belum 100%
        if ($indicators->sum('weight') != 100) {
            return redirect()->route('admin.master.kpi-indicator.index')
                ->with('error', 'Gagal: Total bobot indikator saat ini adalah ' . $indicators->sum('weight') . '%. Harus tepat 100% untuk memulai penilaian.');
        }

        return view('admin.transaksi.kpi.create', compact('employees', 'indicators'));
    }

    /**
     * 3. STORE: Proses Simpan & Kalkulasi Skor Berdasarkan Bobot
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|digits:2',
            'year'    => 'required|integer|min:2020',
            'scores'  => 'required|array',
            'feedback'=> 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $indicators = KpiIndicator::where('is_active', true)->get();
            $finalScore = 0;
            $scoreDetails = [];

            foreach ($indicators as $ind) {
                $inputScore = $request->scores[$ind->id] ?? 0;
                
                // Rumus Perhitungan Terbobot: (Nilai Input x Bobot Indikator / 100)
                $weightedResult = ($inputScore * $ind->weight) / 100;
                $finalScore += $weightedResult;

                // Snapshot data: Menyimpan nama & bobot saat ini agar histori tetap valid meski master diubah
                $scoreDetails[] = [
                    'indicator_id'   => $ind->id,
                    'indicator_name' => $ind->name,
                    'weight'         => $ind->weight,
                    'score'          => (float)$inputScore,
                    'weighted_result'=> round($weightedResult, 2)
                ];
            }

            $kpi = Kpi::updateOrCreate(
                [
                    'user_id'      => $request->user_id,
                    'period_month' => $request->month,
                    'period_year'  => $request->year,
                ],
                [
                    'score_details' => $scoreDetails,
                    'final_score'   => round($finalScore, 2),
                    'feedback'      => $request->feedback,
                    'rated_by'      => Auth::id(),
                    'status'        => 'published' 
                ]
            );

            DB::commit();
            return redirect()->route('admin.transaksi.kpi.index')
                ->with('success', "Penilaian KPI {$kpi->user->name} periode {$kpi->period_label} berhasil disimpan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * 4. SHOW: Lihat Rincian Hasil Penilaian
     */
    public function show($id)
    {
        $kpi = Kpi::with(['user.position', 'user.department', 'reviewer'])->findOrFail($id);
        return view('admin.transaksi.kpi.show', compact('kpi'));
    }

    /**
     * 5. EDIT: Form Perubahan Nilai Existing
     */
    public function edit($id)
    {
        $kpi = Kpi::findOrFail($id);
        $employees = User::where('id', $kpi->user_id)->get(); // Karyawan yang sedang diedit
        $indicators = KpiIndicator::where('is_active', true)->get();

        return view('admin.transaksi.kpi.edit', compact('kpi', 'employees', 'indicators'));
    }

    /**
     * 6. UPDATE: Simpan Perubahan Penilaian
     */
    public function update(Request $request, $id)
    {
        // Validasi ID untuk memastikan data ada
        $kpi = Kpi::findOrFail($id);
        
        // Memanggil store logic untuk memproses ulang nilai
        return $this->store($request);
    }

    /**
     * 7. PRINT: Tampilan Khusus Cetak (A4 Friendly)
     */
    public function print($id)
    {
        $kpi = Kpi::with(['user.position', 'user.department', 'user.branch', 'reviewer'])
                  ->findOrFail($id);

        return view('admin.transaksi.kpi.print', compact('kpi'));
    }

    /**
     * 8. DESTROY: Hapus Data Penilaian
     */
    public function destroy($id)
    {
        $kpi = Kpi::findOrFail($id);
        $name = $kpi->user->name;
        $kpi->delete();

        return back()->with('success', "Data penilaian untuk {$name} telah dihapus.");
    }
}