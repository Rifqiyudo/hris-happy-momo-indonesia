<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage; // Tambahkan ini
use Carbon\Carbon;

class AnnouncementController extends Controller
{
    /**
     * 1. INDEX: Monitoring Pengumuman
     */
    public function index()
    {
        $query = Announcement::with(['department', 'author'])->latest();

        if (Auth::user()->role === 'pic') {
            $query->where(function($q) {
                $q->where('department_id', Auth::user()->department_id)
                  ->orWhere('target', 'all');
            });
        }

        $announcements = $query->paginate(10);
        return view('admin.master.announcement.index', compact('announcements'));
    }

    /**
     * 2. CREATE: Form Tambah Pengumuman
     */
    public function create()
    {
        $departments = Department::orderBy('name', 'asc')->get();
        return view('admin.master.announcement.create', compact('departments'));
    }

    /**
     * 3. STORE: Eksekusi Simpan Pengumuman dengan Lampiran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'type'          => 'required|in:info,warning,danger',
            'target'        => 'required|in:all,employee,pic',
            'department_id' => 'nullable|exists:departments,id',
            'is_active'     => 'required|boolean',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:5120', // Max 5MB
        ]);

        try {
            if ($validated['target'] === 'all') {
                $validated['department_id'] = null;
            }

            $announcement = new Announcement($validated);
            $announcement->user_id = Auth::id();

            // Logika Upload File
            if ($request->hasFile('attachment')) {
                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('announcements', $filename, 'public');
                $announcement->file_path = $path;
            }

            $announcement->save();

            return redirect()->route('admin.master.announcement.index')
                             ->with('success', 'Pengumuman baru berhasil diterbitkan.');

        } catch (\Exception $e) {
            Log::error("Error Store Announcement: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal menyimpan data pengumuman.');
        }
    }

    /**
     * 4. EDIT: Form Ubah Pengumuman
     */
    public function edit($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        if (Auth::user()->role === 'pic' && $announcement->user_id !== Auth::id()) {
            abort(403, 'Otoritas ditolak.');
        }

        $departments = Department::orderBy('name', 'asc')->get();
        return view('admin.master.announcement.edit', compact('announcement', 'departments'));
    }

    /**
     * 5. UPDATE: Simpan Perubahan dan Update File
     */
    public function update(Request $request, $id)
    {
        $announcement = Announcement::findOrFail($id);
        
        if (Auth::user()->role === 'pic' && $announcement->user_id !== Auth::id()) {
            return redirect()->route('admin.master.announcement.index')->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'title'         => 'required|string|max:255',
            'content'       => 'required|string',
            'type'          => 'required|in:info,warning,danger',
            'target'        => 'required|in:all,employee,pic',
            'department_id' => 'nullable|exists:departments,id',
            'is_active'     => 'required|boolean',
            'attachment'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,docx,xlsx|max:5120',
        ]);

        try {
            if ($validated['target'] === 'all') {
                $validated['department_id'] = null;
            }

            // Update File Jika Ada Lampiran Baru
            if ($request->hasFile('attachment')) {
                // Hapus file lama jika ada
                if ($announcement->file_path) {
                    Storage::disk('public')->delete($announcement->file_path);
                }

                $file = $request->file('attachment');
                $filename = time() . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('announcements', $filename, 'public');
                $announcement->file_path = $path;
            }

            $announcement->update(array_diff_key($validated, ['attachment' => '']));
            $announcement->save();

            return redirect()->route('admin.master.announcement.index')
                             ->with('success', 'Perubahan pengumuman berhasil disimpan.');

        } catch (\Exception $e) {
            Log::error("Error Update Announcement: " . $e->getMessage());
            return back()->withInput()->with('error', 'Gagal memperbarui data.');
        }
    }

    /**
     * 6. TOGGLE STATUS
     */
    public function toggleStatus($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->is_active = !$announcement->is_active;
        $announcement->save();

        return back()->with('success', 'Status pengumuman berhasil diubah.');
    }

    /**
     * 7. DESTROY: Hapus Data & File Fisik
     */
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        
        if (Auth::user()->role === 'pic' && $announcement->user_id !== Auth::id()) {
            return back()->with('error', 'Akses ditolak.');
        }

        try {
            // Hapus file dari storage sebelum hapus database
            if ($announcement->file_path) {
                Storage::disk('public')->delete($announcement->file_path);
            }

            $announcement->delete();
            return back()->with('success', 'Pengumuman dan lampiran berhasil dihapus.');

        } catch (\Exception $e) {
            Log::error("Error Delete Announcement: " . $e->getMessage());
            return back()->with('error', 'Gagal menghapus data.');
        }
    }
}