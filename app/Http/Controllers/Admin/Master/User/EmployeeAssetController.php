<?php

namespace App\Http\Controllers\Admin\Master\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmployeeAsset;
use Illuminate\Support\Facades\Log;

class EmployeeAssetController extends Controller
{
    /**
     * Proses pengembalian aset dari karyawan ke perusahaan
     */
    public function returnAsset(Request $request, $id)
    {
        try {
            $asset = EmployeeAsset::findOrFail($id);

            // Ubah status aset tidak lagi dipakai oleh user
            $asset->update([
                'status'      => 'returned',
                'return_date' => now(),
                'condition'   => $request->condition ?? $asset->condition, // good / damaged
                'description' => $asset->description . ' | Dikembalikan pada ' . now()->format('d/m/Y')
            ]);

            return back()->with('success', 'Aset ' . $asset->asset_name . ' berhasil dikembalikan ke inventaris perusahaan.');
            
        } catch (\Exception $e) {
            Log::error("Asset Return Error: " . $e->getMessage());
            return back()->with('error', 'Gagal memproses pengembalian aset.');
        }
    }
}