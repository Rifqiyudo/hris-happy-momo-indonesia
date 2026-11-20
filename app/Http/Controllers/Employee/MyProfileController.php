<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Hash, DB, Log};
use App\Models\{User, Payroll, AssetAssignment};
use Barryvdh\DomPDF\Facade\Pdf; // Import Facade PDF
use Carbon\Carbon;

class MyProfileController extends Controller
{
    /**
     * 1. HALAMAN PROFILE UTAMA (ESS PORTAL)
     * Menampilkan data diri, daftar aset yang dibawa, dan arsip slip gaji.
     */
    public function index()
    {
        // Mengambil data user secara fresh beserta relasi cabangnya
        $user = User::with(['branch', 'department', 'position'])
                    ->findOrFail(Auth::id());

        // --- FITUR ESS: ASSETS ---
        $myAssets = AssetAssignment::with('asset')
                    ->where('user_id', $user->id)
                    ->whereNull('return_date')
                    ->get();

        // --- FITUR ESS: PAYROLL ARCHIVE ---
        $myPayrolls = Payroll::where('user_id', $user->id)
                    ->whereIn('status', ['paid', 'published'])
                    ->orderByDesc('year')
                    ->orderByDesc('month')
                    ->take(6) 
                    ->get();

        // Shortcut slip gaji terakhir
        $lastPayroll = $myPayrolls->first();

        return view('employee.profile.index', compact('user', 'myAssets', 'myPayrolls', 'lastPayroll'));
    }

    /**
     * 2. HALAMAN EDIT PROFILE
     */
    public function edit()
    {
        $user = User::findOrFail(Auth::id());
        return view('employee.profile.edit', compact('user'));
    }

    /**
     * 3. PROSES UPDATE PROFILE
     */
    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:15|unique:users,phone,' . $user->id,
            'avatar'   => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'password' => 'nullable|min:8|confirmed',
        ], [
            'avatar.max' => 'Ukuran foto maksimal adalah 2MB.',
            'avatar.image' => 'Berkas harus berupa gambar.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'phone.unique' => 'Nomor WhatsApp sudah terdaftar di sistem.',
        ]);

        try {
            DB::beginTransaction();

            $user->name = $request->name;
            $user->phone = $request->phone;

            if ($request->hasFile('avatar')) {
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                $file = $request->file('avatar');
                $filename = 'avatar_' . ($user->nik ?? $user->id) . '_' . time() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('avatars', $filename, 'public');
                
                $user->avatar = $path;
            }

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return redirect()->route('employee.profile.index')
                             ->with('success', 'Profil Anda berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal update profil user ID " . Auth::id() . ": " . $e->getMessage());

            return back()->with('error', 'Terjadi kesalahan sistem saat memperbarui data.')
                         ->withInput();
        }
    }

    /**
     * 4. DOWNLOAD SURAT KETERANGAN KERJA (ESS NEW FEATURE)
     * Memungkinkan karyawan mendownload surat keterangan kerja dalam format PDF.
     */
    public function downloadCertificate()
    {
        try {
            $user = User::with(['branch', 'department', 'position'])->findOrFail(Auth::id());

            // Validasi masa kerja (Contoh: minimal 3 bulan)
            $joinDate = Carbon::parse($user->join_date);
            if ($joinDate->diffInMonths(now()) < 3) {
                return back()->with('error', 'Fitur ini tersedia bagi karyawan dengan masa kerja minimal 3 bulan.');
            }

            $data = [
                'user'       => $user,
                'date'       => now()->translatedFormat('d F Y'),
                'ref_number' => 'SKK/' . ($user->nik ?? '000') . '/' . date('m/Y'),
            ];

            // Generate PDF menggunakan view khusus
            $pdf = Pdf::loadView('employee.profile.pdf_certificate', $data);
            
            // Set paper to A4
            $pdf->setPaper('a4', 'portrait');

            Log::info("User ID " . Auth::id() . " mendownload Surat Keterangan Kerja.");

            return $pdf->download('Surat_Keterangan_Kerja_' . ($user->nik ?? $user->id) . '.pdf');

        } catch (\Exception $e) {
            Log::error("Gagal download sertifikat user ID " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Gagal membuat dokumen PDF.');
        }
    }
}