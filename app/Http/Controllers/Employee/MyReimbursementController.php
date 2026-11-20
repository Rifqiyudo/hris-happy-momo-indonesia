<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Reimbursement;

class MyReimbursementController extends Controller
{
    /**
     * 1. LIST RIWAYAT KLAIM
     */
    public function index()
    {
        // Menggunakan pagination agar aplikasi tetap ringan saat data banyak
        $reimbursements = Reimbursement::where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('employee.reimbursement.index', compact('reimbursements'));
    }

    /**
     * 2. FORM PENGAJUAN (CREATE)
     */
    public function create()
    {
        return view('employee.reimbursement.create');
    }

    /**
     * 3. PROSES SIMPAN (STORE)
     */
    public function store(Request $request)
    {
        // Validasi yang lebih ketat untuk keamanan file
        $request->validate([
            'item_name'   => 'required|string|max:100',
            'category'    => 'required|string', // Sesuai migration terbaru
            'date'        => 'required|date',
            'amount'      => 'required|numeric|min:1',
            'proof_file'  => 'required|image|mimes:jpeg,png,jpg|max:2048', 
            'description' => 'nullable|string|max:255',
        ]);

        $path = null;
        if ($request->hasFile('proof_file')) {
            // Penamaan file yang unik: idUser_timestamp_acak.ext
            $file = $request->file('proof_file');
            $filename = Auth::id() . '_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('reimbursements', $filename, 'public');
        }

        Reimbursement::create([
            'user_id'     => Auth::id(),
            'category'    => $request->category,
            'item_name'   => $request->item_name,
            'date'        => $request->date,
            'amount'      => $request->amount,
            'proof_file'  => $path,
            'description' => $request->description,
            'status'      => 'pending',
        ]);

        return redirect()->route('employee.reimbursement.index')
            ->with('success', 'Klaim reimbursement berhasil diajukan! Tunggu verifikasi admin.');
    }

    /**
     * 4. DETAIL PENGAJUAN (SHOW) - Perbaikan Error Undefined Method
     */
    public function show($id)
    {
        // Security: Hanya bisa melihat data miliknya sendiri
        $reimbursement = Reimbursement::where('user_id', Auth::id())
            ->findOrFail($id);

        return view('employee.reimbursement.show', compact('reimbursement'));
    }

    /**
     * 5. BATALKAN/HAPUS (DESTROY)
     */
    public function destroy($id)
    {
        // Hanya bisa menghapus jika status masih 'pending'
        $reimbursement = Reimbursement::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);

        // Hapus file fisik di storage agar tidak menumpuk
        if ($reimbursement->proof_file) {
            Storage::disk('public')->delete($reimbursement->proof_file);
        }

        $reimbursement->delete();

        return redirect()->route('employee.reimbursement.index')
            ->with('success', 'Pengajuan klaim telah dibatalkan.');
    }
}