<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Chat, ChatMessage, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, Storage, Log, DB};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Menampilkan daftar percakapan admin dengan performa tinggi.
     */
    public function index()
    {
        $adminId = Auth::id();
        
        $chats = Chat::where('hrd_id', $adminId)
            ->with([
                // Ditambahkan is_active untuk indikator titik hijau (online/aktif) di UI Sidebar
                'user:id,name,avatar,is_active', 
                'lastMessage' => fn($q) => $q->latest()
            ]) 
            ->withCount(['messages as unread_count' => function($query) use ($adminId) {
                $query->where('is_read', false)->where('sender_id', '!=', $adminId);
            }])
            ->latest('updated_at')
            ->paginate(15);
                     
        return view('admin.chat.index', compact('chats'));
    }

    /**
     * AJAX Search: Mencari karyawan.
     */
    public function searchEmployee(Request $request)
    {
        $term = $request->get('q');
        
        if (!$term || strlen($term) < 2) return response()->json([]);
        
        $employees = User::where('role', 'employee')
            ->with(['position:id,name', 'branch:id,name']) 
            ->where(fn($q) => $q->where('name', 'like', "%{$term}%")->orWhere('nik', 'like', "%{$term}%"))
            ->limit(10)
            ->get();

        return response()->json($employees->map(fn($user) => [
            'id'         => $user->id,
            'name'       => $user->name,
            'avatar_url' => $user->avatar_url,
            'position'   => $user->position->name ?? 'Staff',
            'branch'     => $user->branch->name ?? 'Pusat'
        ]));
    }

    /**
     * Memulai percakapan baru.
     */
    public function startChat($userId)
    {
        $user = User::where('id', $userId)->where('role', 'employee')->firstOrFail();
        
        $chat = Chat::firstOrCreate(
            ['user_id' => $user->id, 'hrd_id' => Auth::id()],
            ['updated_at' => now()] 
        );

        return redirect()->route('admin.chat.show', $chat->id);
    }

    /**
     * Menampilkan detail percakapan.
     */
    public function show($chatId)
    {
        try {
            $chat = Chat::where('id', $chatId)
                        ->where('hrd_id', Auth::id())
                        // Eager load jabatan (position) dan cabang (branch) agar muncul di Header Chat UI Baru
                        ->with(['user:id,name,avatar,is_active,position_id,branch_id', 'user.position', 'user.branch'])
                        ->firstOrFail();

            // Tandai pesan sebagai dibaca (Atomic Transaction)
            DB::transaction(fn() => ChatMessage::where('chat_id', $chat->id)
                           ->where('sender_id', '!=', Auth::id())
                           ->where('is_read', false)
                           ->update(['is_read' => true]));

            $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();
            
            return view('admin.chat.show', compact('chat', 'messages'));
            
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.chat.index')->with('error', 'Percakapan tidak ditemukan.');
        }
    }

    /**
     * Menyimpan pesan dan lampiran file dengan penamaan unik.
     */
    public function store(Request $request, $chatId)
    {
        // Validasi diperbarui: Menambahkan dukungan file excel (xls, xlsx) sesuai dengan form di file Blade
        $request->validate([
            'message' => 'required_without:file|nullable|string|max:5000',
            'file'    => 'required_without:message|nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $chatId) {
                $chat = Chat::where('id', $chatId)->where('hrd_id', Auth::id())->firstOrFail();

                $filePath = null;
                
                // Logika Upload File yang Diperbarui
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    
                    // Sanitasi nama file untuk menghindari spasi atau karakter yang bisa merusak link download
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $safeName = Str::slug($originalName) . '.' . $extension;

                    // Simpan file dengan nama unik menggunakan timestamp & slug
                    $filePath = $file->storeAs(
                        'chat_files/' . Carbon::now()->format('Y/m'),
                        uniqid() . '_' . $safeName,
                        'public'
                    );
                }

                ChatMessage::create([
                    'chat_id'   => $chat->id,
                    'sender_id' => Auth::id(),
                    'message'   => $request->message,
                    'file_path' => $filePath,
                    'is_read'   => false,
                ]);

                // Update timestamp pada parent (Tabel Chat) agar naik ke urutan teratas di sidebar
                $chat->touch(); 

                return redirect()->route('admin.chat.show', $chatId);
            });
        } catch (\Exception $e) {
            Log::error("Error Chat ID {$chatId}: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengirim pesan atau file.');
        }
    }
}