<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\KpiIndicator;
use Illuminate\Http\Request;

class KpiIndicatorController extends Controller
{
    public function index()
    {
        $indicators = KpiIndicator::latest()->get();
        $totalWeight = KpiIndicator::where('is_active', true)->sum('weight');
        
        return view('admin.master.kpi_indicator.index', compact('indicators', 'totalWeight'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1|max:100',
            'description' => 'nullable|string'
        ]);

        // Validasi: Total bobot tidak boleh > 100%
        $currentTotal = KpiIndicator::where('is_active', true)->sum('weight');
        if (($currentTotal + $request->weight) > 100) {
            return back()->with('error', "Gagal! Total bobot akan melebihi 100%. Sisa kuota bobot: " . (100 - $currentTotal) . "%");
        }

        KpiIndicator::create($request->all());

        return back()->with('success', 'Indikator KPI berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $indicator = KpiIndicator::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'weight' => 'required|integer|min:1|max:100',
        ]);

        // Cek total bobot jika diupdate (kecuali dirinya sendiri)
        $totalOther = KpiIndicator::where('is_active', true)->where('id', '!=', $id)->sum('weight');
        if (($totalOther + $request->weight) > 100) {
            return back()->with('error', "Gagal! Total bobot melebihi 100%.");
        }

        $indicator->update($request->all());

        return back()->with('success', 'Indikator KPI berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $indicator = KpiIndicator::findOrFail($id);
        $indicator->delete();

        return back()->with('success', 'Indikator berhasil dihapus.');
    }
}