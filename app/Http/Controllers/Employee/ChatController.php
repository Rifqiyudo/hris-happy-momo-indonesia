<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\{Chat, ChatMessage, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Log, Storage};
use Carbon\Carbon;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Menampilkan daftar percakapan karyawan dengan HRD
     */
    public function index()
    {
        $userId = Auth::id();

        // ====================================================================
        // FITUR ANTI KOSONG (AUTO-CREATE THREAD)
        // ====================================================================
        // Jika karyawan belum memiliki ruang percakapan dengan HRD,
        // sistem akan otomatis membuatkan 1 ruang pesan default.
        $chatExists = Chat::where('user_id', $userId)->exists();
        
        if (!$chatExists) {
            // Cari akun Administrator HRD
            $admin = User::where('role', 'admin')->first();
            
            if ($admin) {
                // Buat ruang percakapan baru
                $newChat = Chat::create([
                    'user_id' => $userId,
                    'hrd_id'  => $admin->id,
                ]);
                
                // Suntikkan pesan sapaan otomatis dari HRD
                ChatMessage::create([
                    'chat_id'   => $newChat->id,
                    'sender_id' => $admin->id,
                    'message'   => 'Halo! Ada yang bisa kami bantu? Silakan sampaikan pertanyaan, pengajuan, atau kendala Anda di sini.',
                    'is_read'   => false,
                ]);
            }
        }

        // ====================================================================
        // PENGAMBILAN DATA CHAT
        // ====================================================================
        $chats = Chat::where('user_id', $userId)
                     ->with([
                         // Menarik data avatar dan status HRD untuk UI sidebar
                         'hrd:id,name,avatar,is_active', 
                         'lastMessage' => fn($q) => $q->latest()
                     ])
                     ->withCount(['messages as unread_count' => function($query) use ($userId) {
                         $query->where('is_read', false)
                               ->where('sender_id', '!=', $userId);
                     }])
                     ->latest('updated_at')
                     ->paginate(15);

        // Cek status HRD Online (Apakah ada Admin/PIC yang beraktivitas dalam 15 menit terakhir)
        // Ini untuk menghidupkan indikator hijau "HRD ONLINE" di pojok kanan atas UI Anda
        $hrdOnline = User::whereIn('role', ['admin', 'pic'])
            ->where('updated_at', '>=', now()->subMinutes(15))
            ->exists();

        return view('employee.chat.index', compact('chats', 'hrdOnline'));
    }

    /**
     * Menampilkan detail pesan percakapan di dalam ruang chat
     */
    public function show($chatId)
    {
        $chat = Chat::where('id', $chatId)
                    ->where('user_id', Auth::id())
                    // Eager loading data HRD lengkap untuk ditampilkan di Header UI Chat
                    ->with(['hrd:id,name,avatar,is_active,position_id,branch_id', 'hrd.position', 'hrd.branch'])
                    ->firstOrFail();

        // Gunakan transaksi database untuk keamanan saat menandai pesan telah dibaca (Read Receipt)
        DB::transaction(function () use ($chat) {
            $chat->messages()
                 ->where('sender_id', '!=', Auth::id())
                 ->where('is_read', false)
                 ->update(['is_read' => true]);
        });

        // Ambil seluruh isi pesan diurutkan dari yang paling lama ke terbaru (Gaya Chat WhatsApp)
        $messages = $chat->messages()->with('sender')->orderBy('created_at', 'asc')->get();

        return view('employee.chat.show', compact('chat', 'messages'));
    }

    /**
     * Mengirim pesan atau melampirkan file ke HRD
     */
    public function store(Request $request, $chatId)
    {
        // Validasi yang mendukung pengiriman teks maupun file dokumen/gambar
        $request->validate([
            'message' => 'nullable|string|max:5000|required_without:file',
            'file'    => 'nullable|file|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx|max:5120',
        ]);

        try {
            return DB::transaction(function () use ($request, $chatId) {
                $chat = Chat::where('id', $chatId)
                            ->where('user_id', Auth::id())
                            ->firstOrFail();

                $filePath = null;
                
                // Logika Upload File
                if ($request->hasFile('file')) {
                    $file = $request->file('file');
                    
                    // Sanitasi nama file agar aman dibaca oleh semua browser
                    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $extension = $file->getClientOriginalExtension();
                    $safeName = Str::slug($originalName) . '.' . $extension;

                    // Simpan file dengan nama unik ke folder public/chat_files
                    $filePath = $file->storeAs(
                        'chat_files/' . Carbon::now()->format('Y/m'),
                        uniqid() . '_' . $safeName,
                        'public'
                    );
                }

                // Masukkan pesan ke database
                ChatMessage::create([
                    'chat_id'   => $chat->id,
                    'sender_id' => Auth::id(),
                    'message'   => $request->message,
                    'file_path' => $filePath,
                    'is_read'   => false,
                ]);

                // Update kolom updated_at pada tabel chats (Push to Top)
                // Ini membuat chat yang baru saja dibalas akan naik ke urutan paling atas di layar HRD
                $chat->touch();

                return redirect()->route('employee.chat.show', $chatId)->with('success', 'Pesan berhasil dikirim.');
            });
        } catch (\Exception $e) {
            Log::error("Karyawan gagal mengirim pesan ke HRD: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat mengirim pesan, silakan coba lagi.');
        }
    }
}