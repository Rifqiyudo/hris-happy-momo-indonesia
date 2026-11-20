<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class Overtime extends Model
{
    use HasFactory;

    /**
     * Nama tabel eksplisit.
     */
    protected $table = 'overtimes';

    /**
     * Mass Assignment: Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id',
        'date',
        'start_time',
        'end_time',
        'duration_minutes',
        'reason',
        'attachment',   // File bukti lembur (Path)
        'status',       // pending, approved, rejected
        'admin_note',   // Alasan dari admin jika ditolak/disetujui
    ];

    /**
     * Casts: Casting tipe data otomatis untuk mempermudah manipulasi object.
     */
    protected $casts = [
        'date' => 'date',
        'duration_minutes' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Menambahkan attribute virtual (Accessors) ke dalam array/JSON model.
     */
    protected $appends = [
        'duration_readable', 
        'status_label', 
        'status_badge', 
        'attachment_url',
        'formatted_start_time',
        'formatted_end_time'
    ];

    // =================================================================
    // RELATIONSHIPS
    // =================================================================

    /**
     * Relasi ke Model User (Setiap lembur dimiliki satu Karyawan).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // =================================================================
    // ACCESSORS (Helpers untuk tampilan Blade)
    // =================================================================

    /**
     * 1. Durasi Format Manusia (Contoh: "1 Jam 30 Menit").
     */
    public function getDurationReadableAttribute(): string
    {
        if (!$this->duration_minutes) return '0 Menit';

        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        $result = [];
        if ($hours > 0) $result[] = "{$hours} Jam";
        if ($minutes > 0) $result[] = "{$minutes} Menit";

        return implode(' ', $result);
    }

    /**
     * 2. Badge Status dengan Style Tailwind (Identik dengan HRD Pintar).
     */
    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
            'rejected' => 'bg-rose-100 text-rose-700 border-rose-200',
            default    => 'bg-amber-100 text-amber-700 border-amber-200',
        };
    }

    /**
     * 3. Label Status Bahasa Indonesia.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default    => 'Menunggu',
        };
    }

    /**
     * 4. URL Lampiran Foto (Akses langsung ke Storage).
     */
    public function getAttachmentUrlAttribute(): ?string
    {
        if ($this->attachment && Storage::disk('public')->exists($this->attachment)) {
            return asset('storage/' . $this->attachment);
        }
        // Return avatar inisial jika tidak ada file (Placeholder)
        return "https://ui-avatars.com/api/?name=Bukti+Lembur&background=f1f5f9&color=cbd5e1";
    }

    /**
     * 5. Formatted Time (Menghilangkan detik saat tampil di Blade).
     * Contoh: 17:00:00 -> 17:00
     */
    public function getFormattedStartTimeAttribute()
    {
        return $this->start_time ? Carbon::parse($this->start_time)->format('H:i') : '-';
    }

    public function getFormattedEndTimeAttribute()
    {
        return $this->end_time ? Carbon::parse($this->end_time)->format('H:i') : '-';
    }

    // =================================================================
    // SCOPES (Mempermudah Query di Controller)
    // =================================================================

    

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', Carbon::now()->month)
                     ->whereYear('date', Carbon::now()->year);
    }

    /**
     * Scope untuk memfilter lembur berdasarkan range tanggal.
     */
    public function scopeBetweenDates($query, $from, $to)
    {
        return $query->whereBetween('date', [$from, $to]);
    }
}