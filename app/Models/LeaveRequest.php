<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'user_id', 
        'type', 
        'start_date', 
        'end_date', 
        'total_days', 
        'reason', 
        'attachment', 
        'status', 
        'approved_by', 
        'admin_note',
        'is_unpaid' // Kolom baru untuk penanda potong gaji
    ];

    /**
     * Casting tipe data kolom.
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'total_days' => 'integer',
        'is_unpaid'  => 'boolean',
    ];

    /**
     * Logic otomatis saat data LeaveRequest dibuat atau diperbarui.
     */
    protected static function booted()
    {
        static::saving(function ($leaveRequest) {
            // Otomatis menghitung total hari jika start_date dan end_date tersedia
            if ($leaveRequest->start_date && $leaveRequest->end_date) {
                $start = Carbon::parse($leaveRequest->start_date);
                $end = Carbon::parse($leaveRequest->end_date);
                $leaveRequest->total_days = $start->diffInDays($end) + 1;
            }
        });
    }

    // =========================================================================
    // RELATIONS
    // =========================================================================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // =========================================================================
    // ACCESSORS (VIRTUAL ATTRIBUTES)
    // =========================================================================

    /**
     * Label status untuk tampilan admin (dengan HTML).
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'Approved'  => '<span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold uppercase">Disetujui</span>',
            'Rejected'  => '<span class="px-3 py-1 bg-rose-100 text-rose-700 rounded-full text-xs font-bold uppercase">Ditolak</span>',
            'Cancelled' => '<span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-full text-xs font-bold uppercase">Dibatalkan</span>',
            default     => '<span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold uppercase">Menunggu</span>',
        };
    }

    /**
     * Badge warna untuk tampilan karyawan (Tailwind classes).
     */
    public function getStatusBadgeAttribute(): string
    {
        return match (strtolower($this->status)) {
            'approved' => 'bg-emerald-50 text-emerald-600 border-emerald-100',
            'rejected' => 'bg-rose-50 text-rose-600 border-rose-100',
            default    => 'bg-amber-50 text-amber-600 border-amber-100',
        };
    }

    /**
     * Helper untuk menampilkan teks status secara bersih tanpa HTML.
     */
    public function getStatusTextAttribute(): string
    {
        return ucfirst($this->status);
    }

    public function getDurationAttribute(): string
    {
        return $this->total_days . ' Hari';
    }

    public function getAttachmentUrlAttribute()
    {
        return $this->attachment ? asset('storage/' . $this->attachment) : null;
    }
}