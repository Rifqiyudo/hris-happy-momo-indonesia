<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveAllowance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeaveAllowanceController extends Controller
{
    /**
     * Update atau Create jatah kuota cuti karyawan tahun berjalan.
     */
    public function updateQuota(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'quota'   => 'required|integer|min:0|max:365',
        ], [
            'quota.required' => 'Jumlah hari jatah harus diisi.',
            'quota.integer'  => 'Format harus berupa angka.',
        ]);

        try {
            /**
             * 2. Simpan Data menggunakan updateOrCreate
             * Mencari berdasarkan user_id dan tahun saat ini.
             * Jika ada, maka update. Jika tidak ada, maka buat baru.
             */
            LeaveAllowance::updateOrCreate(
                [
                    'user_id' => $request->user_id,
                    'year'    => date('Y') // Kuota berlaku untuk tahun berjalan
                ],
                [
                    'quota' => $request->quota,
                    'note'  => 'Diperbarui oleh Admin pada ' . now()->format('d/m/Y H:i'),
                ]
            );

            return redirect()->back()->with('success', 'Jatah cuti tahun ' . date('Y') . ' berhasil diperbarui!');

        } catch (\Exception $e) {
            // Log error untuk mempermudah debugging jika gagal
            Log::error("Update Leave Allowance Error: " . $e->getMessage());

            return redirect()->back()->with('error', 'Gagal memperbarui jatah cuti: ' . $e->getMessage());
        }
    }
}