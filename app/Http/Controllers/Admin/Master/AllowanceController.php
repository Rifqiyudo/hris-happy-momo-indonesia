<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Allowance;
use Illuminate\Http\Request;

class AllowanceController extends Controller
{
    public function index()
    {
        $allowances = Allowance::all();
        return view('admin.master.allowance.index', compact('allowances'));
    }

    public function create()
    {
        return view('admin.master.allowance.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:daily,monthly',
        ]);

        Allowance::create($request->all());

        return redirect()->route('admin.master.allowance.index')->with('success', 'Tunjangan ditambahkan');
    }

    public function edit($id)
    {
        $allowance = Allowance::findOrFail($id);
        return view('admin.master.allowance.edit', compact('allowance'));
    }

    public function update(Request $request, $id)
    {
        $allowance = Allowance::findOrFail($id);
        
        $request->validate([
            'name' => 'required',
            'amount' => 'required|numeric',
            'type' => 'required|in:daily,monthly',
        ]);

        $allowance->update($request->all());

        return redirect()->route('admin.master.allowance.index')->with('success', 'Tunjangan diperbarui');
    }

    public function destroy($id)
    {
        Allowance::destroy($id);
        return redirect()->back()->with('success', 'Tunjangan dihapus');
    }
}