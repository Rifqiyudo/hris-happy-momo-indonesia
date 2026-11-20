<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleController extends Controller
{
    // ... method index() ...

    public function store(Request $request)
    {
        $request->validate([
            'name'         => 'required|unique:roles,name',
            'display_name' => 'required|string|max:255',
            'permissions'  => 'nullable|array' // Array dari checkbox modal
        ]);

        try {
            DB::beginTransaction();
            
            // Buat Role Baru
            $role = Role::create([
                'name'         => strtolower(trim($request->name)),
                'display_name' => $request->display_name,
                'guard_name'   => 'web'
            ]);

            // Sinkronisasi Hak Akses (Jika ada checkbox yang dicentang)
            if ($request->has('permissions')) {
                // Pastikan permission sudah ada di database, jika belum otomatis terbuat
                foreach ($request->permissions as $permName) {
                    Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                }
                $role->syncPermissions($request->permissions);
            }

            DB::commit();
            return back()->with('success', 'Role dan Hak Akses berhasil disimpan.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Role Store Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan role: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);
        
        $request->validate([
            'display_name' => 'required|string|max:255',
            'permissions'  => 'nullable|array'
        ]);

        try {
            DB::beginTransaction();
            
            $role->update(['display_name' => $request->display_name]);

            // Role admin tidak boleh dikurangi hak aksesnya (Superadmin Bypass)
            if ($role->name !== 'admin') {
                if ($request->has('permissions')) {
                    foreach ($request->permissions as $permName) {
                        Permission::firstOrCreate(['name' => $permName, 'guard_name' => 'web']);
                    }
                    $role->syncPermissions($request->permissions);
                } else {
                    $role->syncPermissions([]); // Kosongkan jika tidak ada yang dicentang
                }
            }

            DB::commit();
            return back()->with('success', 'Konfigurasi Hak Akses diperbarui.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("Role Update Error: " . $e->getMessage());
            return back()->with('error', 'Gagal update role: ' . $e->getMessage());
        }
    }
}