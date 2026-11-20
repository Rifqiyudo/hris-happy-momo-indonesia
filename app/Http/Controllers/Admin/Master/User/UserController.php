<?php

namespace App\Http\Controllers\Admin\Master\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Position;
use App\Models\Branch;
use App\Models\Department;
use App\Models\Shift;
use App\Models\EmployeeAsset; 
use App\Models\Asset; // [BARU] Tambahkan model master Asset
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Support\Str; 
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $statusFilter = $request->get('status', 'active'); 
        $branchFilter = $request->get('branch_id');
        $employmentFilter = $request->get('employee_status');
        $expiryFilter = $request->get('contract_expiry'); 

        // Update Eager Loading: Ganti branch menjadi branchDetail
        $query = User::with(['position', 'branchDetail', 'department'])
                    ->whereIn('role', ['employee', 'pic', 'admin']);

        $currentUser = Auth::user();
        if ($currentUser && $currentUser->role === 'pic') {
            $query->where('department_id', $currentUser->department_id);
        }

        if ($branchFilter) $query->where('branch_id', $branchFilter);
        if ($employmentFilter) $query->where('employee_status', $employmentFilter);

        if ($employmentFilter === 'Contract' && $expiryFilter) {
            $query->whereNotNull('join_date')->whereNotNull('contract_duration');
            if ($expiryFilter === 'expired') {
                $query->whereRaw('DATE_ADD(join_date, INTERVAL contract_duration MONTH) < NOW()');
            } elseif ($expiryFilter === 'urgent') {
                $query->whereRaw('DATE_ADD(join_date, INTERVAL contract_duration MONTH) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 30 DAY)');
            }
        }

        $query->where('is_active', $statusFilter === 'active');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('educational_major', 'like', "%{$search}%");
            });
        }
            
        $users = $query->latest()->paginate(15)->withQueryString();

        $users->getCollection()->transform(function ($user) {
            if ($user->employee_status === 'Contract' && $user->join_date && $user->contract_duration) {
                $expiryDate = Carbon::parse($user->join_date)->addMonths($user->contract_duration);
                $user->days_left = round(now()->diffInDays($expiryDate, false));
            }
            return $user;
        });

        $branches = Branch::orderBy('name', 'asc')->get();
        $stats = [
            'total'    => User::count(),
            'active'   => User::where('is_active', true)->count(),
            'resigned' => User::where('is_active', false)->count(),
            'new'      => User::whereMonth('created_at', now()->month)->count()
        ];

        return view('admin.master.user.index', compact('users', 'stats', 'statusFilter', 'branches'));
    }

    public function importForm() { return view('admin.master.user.import'); }

    public function create()
    {
        $positions   = Position::orderBy('name', 'asc')->get();
        $branches    = Branch::where('is_active', true)->orderBy('name', 'asc')->get();          
        $departments = Department::orderBy('name', 'asc')->get();   
        $shifts      = Shift::orderBy('name', 'asc')->get();
        
        return view('admin.master.user.create', compact('positions', 'branches', 'departments', 'shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik'               => 'required|alpha_dash|unique:users,nik',
            'name'              => 'required|string|max:255',
            'join_date'         => 'required|date',
            'branch_id'         => 'required|exists:branches,id',
            'email'             => 'nullable|email|unique:users,email',
            'educational_major' => 'nullable|string|max:255',
            'asset_name'        => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();
            $data = $request->all();
            
            $data['password'] = Hash::make($request->password ?? $request->nik);
            
            if (!$request->filled('email')) {
                $data['email'] = strtolower(Str::slug($request->name, '')) . $request->nik . '@happymomo.id';
            }

            $data['is_active'] = true;
            $data['barcode_token'] = Str::random(32);

            if ($request->hasFile('avatar')) {
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            $user = User::create($data);

            if ($request->filled('asset_name')) {
                EmployeeAsset::create([
                    'user_id'             => $user->id,
                    'asset_name'          => $request->asset_name,
                    'asset_brand'         => $request->asset_brand ?? '-',
                    'asset_serial_number' => $request->asset_serial_number,
                    'category'            => $request->asset_category ?? 'electronics',
                    'purchase_price'      => $request->asset_purchase_price ?? 0,
                    'condition'           => 'good',
                    'status'              => 'used',
                    'handover_date'       => $request->asset_handover_date ?? now(),
                    'description'         => 'Inventaris awal.'
                ]);
            }

            DB::commit();
            return redirect()->route('admin.master.user.index')->with('success', 'Karyawan & Aset Inventaris didaftarkan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Store User Error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal Simpan: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        // Panggil relasi masterAssets (baru) selain assets/employeeAssets (lama)
        $user = User::with(['position', 'branchDetail', 'department', 'shift', 'salary', 'documents', 'assets', 'masterAssets'])->findOrFail($id);
        
        if ($user->join_date) {
            $user->service_period = Carbon::parse($user->join_date)->diffForHumans(['parts' => 2, 'join' => true]);
        }
        
        return view('admin.master.user.show', compact('user'));
    }

    // [FITUR BARU] Controller untuk menampilkan halaman cetak A4 Minimalis
    public function print($id)
    {
        // Load seluruh relasi data pribadi, unit, departemen, dan inventaris
        $user = User::with(['position', 'branchDetail', 'department', 'assets', 'masterAssets'])->findOrFail($id);
        
        // Kalkulasi masa kerja sebelum dilempar ke view print
        if ($user->join_date) {
            $user->service_period = Carbon::parse($user->join_date)->diffForHumans(['parts' => 2, 'join' => true]);
        }

        // Return ke view khusus 'print.blade.php' yang tadi telah dibuat
        return view('admin.master.user.print', compact('user'));
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $positions   = Position::orderBy('name', 'asc')->get();
        $branches    = Branch::where('is_active', true)->orderBy('name', 'asc')->get();          
        $departments = Department::orderBy('name', 'asc')->get();   
        $shifts      = Shift::orderBy('name', 'asc')->get();
        
        // [FITUR BARU] Tarik aset dari master Manajemen Aset yang menganggur (Available / user_id NULL)
        $availableAssets = Asset::whereNull('user_id')->orWhere('status', 'available')->get();

        return view('admin.master.user.edit', compact('user', 'positions', 'branches', 'departments', 'shifts', 'availableAssets'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nik'       => 'required|unique:users,nik,'.$id,
            'email'     => 'required|email|unique:users,email,'.$id,
            'name'      => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            // Validasi opsional untuk input aset baru
            'master_asset_id' => 'nullable|exists:assets,id'
        ]);

        try {
            DB::beginTransaction();
            
            $data = $request->except(['password', 'avatar', 'master_asset_id']); 
            
            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if ($request->hasFile('avatar')) {
                if ($user->avatar) Storage::disk('public')->delete($user->avatar);
                $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
            }

            if ($request->employee_status !== 'Contract') {
                $data['contract_duration'] = null;
            }

            $user->update($data);
            
            // [FITUR BARU] ALOKASI ASET (JIKA HRD MEMILIH ASET DARI DROPDOWN)
            if ($request->filled('master_asset_id')) {
                $asset = Asset::find($request->master_asset_id);
                if ($asset && ($asset->status === 'available' || is_null($asset->user_id))) {
                    $asset->update([
                        'user_id' => $user->id,
                        'status' => 'assigned',
                        'assigned_date' => now()
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('admin.master.user.index')->with('success', 'Profil dan alokasi aset berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Update User Error: " . $e->getMessage());
            return back()->withInput()->with('error', 'Terjadi kesalahan sistem saat menyimpan profil.');
        }
    }

    public function importProcess(Request $request)
    {
        $request->validate(['file_csv' => 'required|mimes:csv,txt|max:5120']);
        
        $file = $request->file('file_csv');
        $fileData = fopen($file->getRealPath(), "r");
        
        fgetcsv($fileData, 2000, ";"); 

        $count = 0;
        DB::beginTransaction();
        try {
            while (($data = fgetcsv($fileData, 2000, ";")) !== FALSE) {
                $nik = trim($data[5] ?? '');
                if (empty($nik)) continue;

                $name  = strtoupper(trim($data[4] ?? 'Karyawan Baru'));
                $email = trim($data[11] ?? '');

                if (empty($email)) {
                    $email = strtolower(str_replace(' ', '', $name)) . $nik . '@happymomo.id';
                }

                User::updateOrCreate(
                    ['nik' => $nik], 
                    [
                        'branch_id'         => trim($data[0]) ?: 1,
                        'department_id'     => trim($data[1]) ?: 1,
                        'position_id'       => trim($data[2]) ?: 1,
                        'name'              => $name,
                        'religion'          => trim($data[6] ?? 'Islam'),
                        'birth_place'       => trim($data[7] ?? '-'),
                        'birth_date'        => $this->formatDate(trim($data[8] ?? '')),
                        'sex'               => (in_array(strtoupper(trim($data[9] ?? '')), ['MALE', 'M', 'L', 'LAKI-LAKI'])) ? 'M' : 'F',
                        'phone'             => trim($data[10] ?? '-'),
                        'email'             => $email,
                        'address'           => trim($data[12] ?? '-'),
                        'domicile'          => trim($data[13] ?? '-'),
                        'last_education'    => trim($data[14] ?? '-'),
                        'educational_major' => trim($data[27] ?? '-'), 
                        'ktp_no'            => $this->cleanNumber($data[15] ?? '-'),
                        'bank_name'         => trim($data[18] ?? 'BCA'),
                        'bank_account_no'   => $this->cleanNumber($data[19] ?? '0'),
                        'bpjs_ketenagakerjaan_no' => $this->cleanNumber($data[20] ?? '0'),
                        'bpjs_kesehatan_no'       => $this->cleanNumber($data[21] ?? '0'),
                        'join_date'         => $this->formatDate(trim($data[22] ?? '')) ?: now(),
                        'employee_status'   => (strtoupper(trim($data[23] ?? '')) == 'P' || strtoupper(trim($data[23] ?? '')) == 'PERMANENT') ? 'Permanent' : 'Contract',
                        'contract_duration' => trim($data[24]) ?: 0,
                        'late_deduction'    => trim($data[25]) ?: 1,
                        'late_tolerance'    => trim($data[26]) ?: 5,
                        'password'          => Hash::make($nik), 
                        'role'              => 'employee',
                        'is_active'         => true,
                        'barcode_token'     => Str::random(32),
                    ]
                );
                $count++;
            }
            DB::commit();
            fclose($fileData);
            return redirect()->route('admin.master.user.index')->with('success', $count . ' Data Karyawan berhasil disinkronisasi.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Import Error: " . $e->getMessage());
            return back()->with('error', 'Gagal Import: ' . $e->getMessage());
        }
    }

    private function formatDate($date) {
        if (empty($date) || $date == '-' || $date == '0') return null;
        try {
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) return $date;
            return Carbon::createFromFormat('d/m/Y', trim($date))->format('Y-m-d');
        } catch (\Exception $e) { return null; }
    }

    private function cleanNumber($val) {
        if(empty($val)) return '0';
        $clean = str_replace([' ', '.', ','], '', $val);
        if (strpos(strtoupper($clean), 'E+') !== false) {
            return sprintf("%.0f", (float)$clean);
        }
        return $clean;
    }

    public function printContractReport(Request $request)
    {
        // Update Eager Loading: Ganti branch menjadi branchDetail
        $users = User::with(['branchDetail', 'department'])->where('employee_status', 'Contract')->get();
        $pdf = Pdf::loadView('admin.master.user.report-pdf', ['users' => $users, 'company' => 'PT. Happy Momo Indonesia'])->setPaper('a4', 'landscape');
        return $pdf->download('Laporan_Kontrak.pdf');
    }

    public function previewCard($id)
    {
        // Update Eager Loading: Ganti branch menjadi branchDetail
        $user = User::with(['position', 'branchDetail', 'department'])->findOrFail($id);
        return view('admin.master.user.print-card', compact('user'));
    }

    public function printCard($id)
    {
        // Update Eager Loading: Ganti branch menjadi branchDetail
        $user = User::with(['position', 'branchDetail', 'department'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.master.user.print-card-pdf', compact('user'))
                    ->setPaper([0, 0, 155.9, 243.7], 'portrait'); 
        
        return $pdf->stream('ID_CARD_' . $user->nik . '.pdf');
    }

    public function destroy($id) {
        $user = User::findOrFail($id);
        if ($user->id === Auth::id()) return back()->with('error', 'Keamanan: Tidak bisa menghapus akun sendiri.');
        
        if ($user->avatar) Storage::disk('public')->delete($user->avatar);
        $user->delete();
        
        return redirect()->route('admin.master.user.index')->with('success', 'Data karyawan dihapus.');
    }
}