<?php

namespace App\Http\Controllers\Admin\Gaji;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Salary; 
use App\Models\Allowance;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class GajiController extends Controller
{
    /**
     * Helper: Membersihkan format Rupiah ke Float murni.
     * Mengonversi input seperti "4.500.000" atau "Rp 4.500.000" menjadi 4500000.
     */
    private function cleanRupiah($value)
    {
        if (empty($value)) return 0;
        if (is_numeric($value)) return (float) $value;
        
        $clean = preg_replace('/[^0-9]/', '', $value);
        return (float) $clean;
    }

    /**
     * ==================================================================================
     * HELPER: Mengambil nilai Tunjangan Jabatan Mutlak dari Master Komponen (Allowances)
     * UPDATE: Sistem Hirarki Prioritas (Pengecualian User -> Jabatan -> Global/All Staff)
     * ==================================================================================
     */
    private function getTjJabatanAuto(User $user)
    {
        // 1. PRIORITAS TERTINGGI (TERKECUALI): Cek spesifik untuk Karyawan ini (Override)
        $userAllowance = Allowance::where('is_active', true)
            ->where('type', 'fixed')
            ->where('name', 'like', '%Jabatan%')
            ->where('user_id', $user->id)
            ->first();
            
        if ($userAllowance) {
            return (float) $userAllowance->amount;
        }

        // 2. PRIORITAS KEDUA: Cek spesifik untuk Jabatan karyawan ini
        if ($user->position_id) {
            $positionAllowance = Allowance::where('is_active', true)
                ->where('type', 'fixed')
                ->where('name', 'like', '%Jabatan%')
                ->where('position_id', $user->position_id)
                ->first();
                
            if ($positionAllowance) {
                return (float) $positionAllowance->amount;
            }
        }

        // 3. PRIORITAS TERENDAH: Cek Tunjangan Global (TARGET: SEMUA KARYAWAN)
        $globalAllowance = Allowance::where('is_active', true)
            ->where('type', 'fixed')
            ->where('name', 'like', '%Jabatan%')
            ->whereNull('user_id')
            ->whereNull('position_id')
            ->first();
            
        if ($globalAllowance) {
            return (float) $globalAllowance->amount;
        }

        return 0; // Jika tidak ada settingan tunjangan sama sekali
    }

    /**
     * 1. INDEX: Daftar Karyawan & Status Komponen Gaji (DENGAN FILTER PERIODE & CABANG)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $periode = $request->input('periode', date('Y-m')); 
        
        $cabang_id = $request->input('cabang_id'); 
        $cabangs = Branch::orderBy('name', 'asc')->get(); 
        
        $query = User::with(['position', 'branch', 'department', 'salary'])
                    ->where('is_active', true)
                    ->whereIn('role', ['employee', 'pic', 'admin']);

        if ($cabang_id) {
            $query->where('branch_id', $cabang_id);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhereHas('branch', function($b) use ($search) {
                      $b->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('position', function($p) use ($search) {
                      $p->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return view('admin.gaji.index', compact('users', 'periode', 'cabangs'));
    }

    /**
     * 2. CREATE: Form Multiple Input (Kolektif)
     */
    public function create()
    {
        $employees = User::with(['branch', 'position']) 
                        ->where('is_active', true)
                        ->whereIn('role', ['employee', 'pic', 'admin'])
                        ->orderBy('name', 'asc')
                        ->get();

        // Menyisipkan data untuk Auto-Fill UI di frontend menggunakan Helper Hirarki
        foreach ($employees as $emp) {
            $emp->tj_jabatan_otomatis = $this->getTjJabatanAuto($emp);
        }

        return view('admin.gaji.create', compact('employees'));
    }

    /**
     * 3. STORE: Proses Simpan Masal & Proteksi Kalkulasi
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|array',
            'user_id.*' => 'required|exists:users,id',
            'base_salary' => 'required|array',
        ], [
            'user_id.required' => 'Daftar karyawan tidak boleh kosong.',
            'base_salary.required' => 'Gaji pokok wajib diisi untuk setiap baris.'
        ]);

        try {
            DB::beginTransaction();

            $processedCount = 0;

            foreach ($request->user_id as $index => $userId) {
                if (empty($userId)) continue;

                $user = User::find($userId);
                if (!$user->branch_id) {
                    throw new \Exception("Karyawan [{$user->name}] gagal diproses karena belum memiliki Unit Kerja/Cabang.");
                }

                // =========================================================
                // SECURITY ENFORCEMENT: Menarik Tunjangan Langsung Dari DB
                // =========================================================
                $tjJabatanVal = $this->getTjJabatanAuto($user);

                $salaryData = [
                    'base_salary'         => $this->cleanRupiah($request->base_salary[$index] ?? 0),
                    'position_allowance'  => $tjJabatanVal, // <-- MUTLAK DARI MASTER TUNJANGAN (BERLAKU HIRARKI)
                    'family_allowance'    => $this->cleanRupiah($request->family_allowance[$index] ?? 0),
                    'meal_allowance'      => $this->cleanRupiah($request->meal_allowance[$index] ?? 0),
                    'transport_allowance' => $this->cleanRupiah($request->transport_allowance[$index] ?? 0),
                    'thr_amount'          => $this->cleanRupiah($request->thr_amount[$index] ?? 0),
                    'bonus_amount'        => $this->cleanRupiah($request->bonus_amount[$index] ?? 0),
                    'other_allowance'     => $this->cleanRupiah($request->other_allowance[$index] ?? 0),
                    'overtime_amount'     => $this->cleanRupiah($request->overtime_amount[$index] ?? 0),
                    'routine_deduction'   => $this->cleanRupiah($request->routine_deduction[$index] ?? 0),
                    'jht_percent'         => 0, 
                    'kes_percent'         => 0, 
                    'jht_deduction'       => $this->cleanRupiah($request->jht_deduction[$index] ?? 0),
                    'kes_deduction'       => $this->cleanRupiah($request->kes_deduction[$index] ?? 0),
                ];

                Salary::updateOrCreate(
                    ['user_id' => $userId],
                    $salaryData
                );

                $processedCount++;
            }

            DB::commit();
            return redirect()->route('admin.gaji.index')->with('success', "Berhasil memperbarui $processedCount komponen gaji karyawan.");
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Gaji Kolektif Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi Kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * 4. EDIT: Form Edit Individu
     */
    public function edit($id)
    {
        $salary = Salary::with('user.position', 'user.branch')->findOrFail($id);
        
        // Memastikan frontend mendapat info terbaru jika ada perubahan master di belakang layar
        $salary->tj_jabatan_otomatis = $this->getTjJabatanAuto($salary->user);

        return view('admin.gaji.edit', compact('salary'));
    }

    /**
     * 5. UPDATE: Proses Update Individu
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'base_salary' => 'required',
        ]);

        try {
            $salary = Salary::findOrFail($id);

            // SECURITY ENFORCEMENT: Menarik Tunjangan Langsung Dari DB
            $tjJabatanVal = $this->getTjJabatanAuto($salary->user);

            $salary->update([
                'base_salary'         => $this->cleanRupiah($request->base_salary),
                'position_allowance'  => $tjJabatanVal, // <-- MUTLAK DARI MASTER TUNJANGAN (BERLAKU HIRARKI)
                'family_allowance'    => $this->cleanRupiah($request->family_allowance),
                'meal_allowance'      => $this->cleanRupiah($request->meal_allowance),
                'transport_allowance' => $this->cleanRupiah($request->transport_allowance),
                'thr_amount'          => $this->cleanRupiah($request->thr_amount),
                'bonus_amount'        => $this->cleanRupiah($request->bonus_amount),
                'other_allowance'     => $this->cleanRupiah($request->other_allowance),
                'overtime_amount'     => $this->cleanRupiah($request->overtime_amount),
                'jht_percent'         => 0,
                'kes_percent'         => 0,
                'jht_deduction'       => $this->cleanRupiah($request->jht_deduction ?? 0),
                'kes_deduction'       => $this->cleanRupiah($request->kes_deduction ?? 0),
                'routine_deduction'   => $this->cleanRupiah($request->routine_deduction),
            ]);

            return redirect()->route('admin.gaji.index')->with('success', 'Komponen gaji ' . $salary->user->name . ' berhasil diperbarui.');
            
        } catch (\Exception $e) {
            Log::error('Update Gaji Individu Error: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * 6. SHOW: Detail Komponen Gaji & Preview Slip
     */
    public function show($id)
    {
        $user = User::with(['salary', 'position', 'branch', 'department'])->findOrFail($id);
        
        if (!$user->salary) {
            return redirect()->route('admin.gaji.create')->with('info', 'Karyawan ini belum memiliki komponen gaji. Silakan atur terlebih dahulu.');
        }

        return view('admin.gaji.show', compact('user'));
    }

    /**
     * 7. DESTROY: Reset Komponen Gaji
     */
    public function destroy($id)
    {
        try {
            $salary = Salary::findOrFail($id);
            $userName = $salary->user->name;
            $salary->delete();

            return redirect()->route('admin.gaji.index')->with('success', "Komponen gaji $userName berhasil direset.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data komponen gaji.');
        }
    }

    /**
     * ==================================================================================
     * EXPORT DATA, DOWNLOAD TEMPLATE & IMPORT CSV (Kolektif)
     * ==================================================================================
     */

    public function exportData()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Export_Data_Gaji_" . date('Ymd_His') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'NIK', 'Nama Karyawan', 'Cabang / Unit Kerja', 'Gaji Pokok', 'Tj Jabatan (Tetap)', 'Tj Keluarga', 
            'Nominal THR', 'Uang Makan (Harian)', 'Transport (Harian)', 'Bonus', 
            'Lainnya', 'Lembur', 'Potongan Rutin', 'BPJS JHT (Rp)', 'BPJS KES (Rp)'
        ];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191));
            fputcsv($file, $columns, ';');

            $employees = User::with(['salary', 'branch'])->where('is_active', true)
                             ->whereIn('role', ['employee', 'pic'])
                             ->get();

            foreach ($employees as $emp) {
                $branchName = 'PUSAT / TIDAK TERDAFTAR';
                if ($emp->branch) {
                    $branchName = $emp->branch->name ?? $emp->branch->nama_cabang ?? $emp->branch->company_name ?? 'PUSAT';
                } elseif ($emp->branch_id) {
                    try {
                        $bDb = DB::table('branches')->where('id', $emp->branch_id)->first();
                        if ($bDb) {
                            $branchName = $bDb->name ?? $bDb->nama_cabang ?? $bDb->company_name ?? 'PUSAT';
                        }
                    } catch (\Exception $e) {}
                }

                fputcsv($file, [
                    $emp->nik, 
                    $emp->name, 
                    strtoupper($branchName), 
                    $emp->salary->base_salary ?? 0,
                    $emp->salary->position_allowance ?? 0,
                    $emp->salary->family_allowance ?? 0,
                    $emp->salary->thr_amount ?? 0,
                    $emp->salary->meal_allowance ?? 0,
                    $emp->salary->transport_allowance ?? 0,
                    $emp->salary->bonus_amount ?? 0,
                    $emp->salary->other_allowance ?? 0,
                    $emp->salary->overtime_amount ?? 0,
                    $emp->salary->routine_deduction ?? 0,
                    $emp->salary->jht_deduction ?? 0,
                    $emp->salary->kes_deduction ?? 0,
                ], ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=Template_Import_Gaji_Kolektif.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'NIK', 'Nama Karyawan', 'Cabang / Unit Kerja', 'Gaji Pokok', 'Tj Jabatan (Tetap)', 'Tj Keluarga', 
            'Nominal THR', 'Uang Makan (Harian)', 'Transport (Harian)', 'Bonus', 
            'Lainnya', 'Lembur', 'Potongan Rutin', 'BPJS JHT (Rp)', 'BPJS KES (Rp)' 
        ];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputs($file, chr(239) . chr(187) . chr(191));
            fputcsv($file, $columns, ';');

            $employees = User::with(['salary', 'branch'])->where('is_active', true)
                             ->whereIn('role', ['employee', 'pic'])
                             ->get();

            foreach ($employees as $emp) {
                $branchName = 'PUSAT / TIDAK TERDAFTAR';
                if ($emp->branch) {
                    $branchName = $emp->branch->name ?? $emp->branch->nama_cabang ?? $emp->branch->company_name ?? 'PUSAT';
                } elseif ($emp->branch_id) {
                    try {
                        $bDb = DB::table('branches')->where('id', $emp->branch_id)->first();
                        if ($bDb) {
                            $branchName = $bDb->name ?? $bDb->nama_cabang ?? $bDb->company_name ?? 'PUSAT';
                        }
                    } catch (\Exception $e) {}
                }

                fputcsv($file, [
                    $emp->nik, 
                    $emp->name, 
                    strtoupper($branchName), 
                    $emp->salary->base_salary ?? 0,
                    $emp->salary->position_allowance ?? 0,
                    $emp->salary->family_allowance ?? 0,
                    $emp->salary->thr_amount ?? 0,
                    $emp->salary->meal_allowance ?? 0,
                    $emp->salary->transport_allowance ?? 0,
                    $emp->salary->bonus_amount ?? 0,
                    $emp->salary->other_allowance ?? 0,
                    $emp->salary->overtime_amount ?? 0,
                    $emp->salary->routine_deduction ?? 0,
                    $emp->salary->jht_deduction ?? 0,
                    $emp->salary->kes_deduction ?? 0,
                ], ';');
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function importProcess(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|mimes:csv,txt,xlsx,xls|max:2048'
        ]);

        try {
            $file = $request->file('file_csv');
            $handle = fopen($file->getRealPath(), "r");
            
            $firstLine = fgets($handle);
            $delimiter = strpos($firstLine, ';') !== false ? ';' : ',';
            rewind($handle); 

            fgetcsv($handle, 1000, $delimiter);

            $successCount = 0;
            $skippedCount = 0;

            DB::beginTransaction();

            while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE) {
                if (empty(array_filter($row))) continue;

                $nik = trim(preg_replace('/[\x00-\x1F\x7F]/', '', $row[0]));
                $user = User::where('nik', $nik)->first();

                if ($user) {
                    // SECURITY ENFORCEMENT: Menarik Tunjangan Langsung Dari DB (Hirarki prioritas berlaku)
                    $tjJabatanValCsv = $this->getTjJabatanAuto($user);

                    Salary::updateOrCreate(
                        ['user_id' => $user->id],
                        [
                            'base_salary'         => $this->cleanRupiah($row[3] ?? 0),
                            'position_allowance'  => $tjJabatanValCsv, // <-- MUTLAK DARI MASTER TUNJANGAN
                            'family_allowance'    => $this->cleanRupiah($row[5] ?? 0),
                            'thr_amount'          => $this->cleanRupiah($row[6] ?? 0),
                            'meal_allowance'      => $this->cleanRupiah($row[7] ?? 0),
                            'transport_allowance' => $this->cleanRupiah($row[8] ?? 0),
                            'bonus_amount'        => $this->cleanRupiah($row[9] ?? 0),
                            'other_allowance'     => $this->cleanRupiah($row[10] ?? 0),
                            'overtime_amount'     => $this->cleanRupiah($row[11] ?? 0),
                            'routine_deduction'   => $this->cleanRupiah($row[12] ?? 0),
                            'jht_deduction'       => $this->cleanRupiah($row[13] ?? 0),
                            'kes_deduction'       => $this->cleanRupiah($row[14] ?? 0),
                            'jht_percent'         => 0, 
                            'kes_percent'         => 0, 
                        ]
                    );
                    $successCount++;
                } else {
                    $skippedCount++;
                }
            }

            fclose($handle);
            DB::commit();

            $message = "Berhasil mengimpor $successCount data komponen gaji.";
            if ($skippedCount > 0) {
                $message .= " ($skippedCount baris dilewati karena NIK tidak ditemukan di sistem).";
            }

            return redirect()->route('admin.gaji.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Gaji CSV Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses CSV. Pastikan format kolom sesuai dengan Template. Detail: ' . $e->getMessage());
        }
    }
}