<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Reimbursement extends Model
{
    use HasFactory;

    protected $table = 'reimbursements';

    /**
     * Mass Assignable Attributes.
     * Menambahkan 'category' dan 'action_at' agar sesuai dengan standar klaim HRIS.
     */
    protected $fillable = [
        'user_id',
        'category',     // Contoh: Medis, Transport, Makan, Parkir
        'item_name',    // Nama barang/keperluan
        'date',         // Tanggal nota
        'amount',       // Nominal uang
        'proof_file',   // Path foto struk
        'description',
        'status',       // pending, approved, rejected
        'admin_note',
        'action_at'     // Tanggal approval/rejection
    ];

    /**
     * Casting tipe data agar otomatis menjadi objek Carbon atau tipe numerik yang tepat.
     */
    protected $casts = [
        'date' => 'date',
        'amount' => 'double',
        'action_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    /**
     * Menambahkan attribute virtual ke output JSON/Array.
     */
    protected $appends = [
        'amount_rupiah', 
        'proof_url', 
        'status_badge', 
        'status_label'
    ];

    // =================================================================
    // RELATIONSHIPS (Relasi antar Tabel)
    // =================================================================

    

    /**
     * Setiap klaim reimbursement dimiliki oleh satu karyawan.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =================================================================
    // ACCESSORS (Fitur Helper untuk View/Blade)
    // =================================================================

    /**
     * 1. Format Rupiah Otomatis
     * Cara panggil: {{ $item->amount_rupiah }}
     */
    public function getAmountRupiahAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * 2. URL Foto Struk Lengkap
     * Cara panggil: <img src="{{ $item->proof_url }}">
     */
    public function getProofUrlAttribute(): ?string
    {
        if ($this->proof_file && Storage::disk('public')->exists($this->proof_file)) {
            return asset('storage/' . $this->proof_file);
        }
        // Placeholder jika struk tidak ada/error
        return "https://ui-avatars.com/api/?name=No+File&background=f1f5f9&color=cbd5e1";
    }

    /**
     * 3. Warna Badge Status (Modern Emerald/Rose Palette)
     * Cara panggil: <span class="{{ $item->status_badge }}">...</span>
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'approved' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
            'rejected' => 'bg-rose-100 text-rose-700 border border-rose-200',
            default    => 'bg-amber-100 text-amber-700 border border-amber-200',
        };
    }

    /**
     * 4. Label Status Bahasa Indonesia
     * Cara panggil: {{ $item->status_label }}
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu',
        };
    }

    // =================================================================
    // SCOPES (Memudahkan Query di Controller)
    // =================================================================

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }
}