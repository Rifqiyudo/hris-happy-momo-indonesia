<?php

namespace App\Models;

use Carbon\Carbon; // [BARU] Untuk pengecekan hari berjalan (Senin - Minggu)
use Illuminate\Database\Eloquent\Factories\HasFactory; // [BARU] Dukungan factory jika dibutuhkan
use Illuminate\Database\Eloquent\Model;

class ShiftSchedule extends Model
{
    use HasFactory; // [BARU]

    // [DIPERBARUI] Mempertahankan field asli & menambahkan kolom baru agar tidak terblokir mass-assignment
    protected $fillable = [
        'day_name',
        'day_number',
        'day_of_week', // [BARU] Kompatibilitas skema migrasi alternatif
        'shift_id',
        'is_off',      // [BARU] Penanda status hari libur / off duty
        'updated_by',  // [BARU] Audit ID admin yang memperbarui pola
    ];

    // [BARU] Casting tipe data otomatis agar konsisten di controller & Blade
    protected $casts = [
        'day_number'  => 'integer',
        'day_of_week' => 'integer',
        'shift_id'    => 'integer',
        'is_off'      => 'boolean',
    ];

    /**
     * Relasi ke Master Shift (Kode Asli Dipertahankan)
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * [BARU] Relasi ke User/Admin yang terakhir memperbarui jadwal
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * [BARU] Scope untuk mengurutkan jadwal dari Senin (1) sampai Minggu (7)
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('day_number', 'asc');
    }

    /**
     * [BARU] Scope khusus hari kerja aktif (bukan libur & memiliki template shift)
     */
    public function scopeWorkDays($query)
    {
        return $query->whereNotNull('shift_id')
            ->where(function ($q) {
                $q->where('is_off', false)->orWhereNull('is_off');
            });
    }

    /**
     * [BARU] Scope khusus hari libur / off duty
     */
    public function scopeOffDays($query)
    {
        return $query->where(function ($q) {
            $q->where('is_off', true)->orWhereNull('shift_id');
        });
    }

    /**
     * [BARU] Accessor nomor hari terpadu (mendukung day_number maupun day_of_week)
     */
    public function getResolvedDayNumberAttribute(): int
    {
        return (int) ($this->day_number ?? $this->day_of_week ?? 0);
    }

    /**
     * [BARU] Accessor nama hari dalam Bahasa Indonesia jika kolom day_name kosong
     */
    public function getResolvedDayNameAttribute(): string
    {
        if (!empty($this->day_name)) {
            return $this->day_name;
        }

        $days = [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ];

        return $days[$this->resolved_day_number] ?? 'Hari ' . $this->resolved_day_number;
    }

    /**
     * [BARU] Accessor pengecekan apakah jadwal pada hari ini berstatus Libur / Off Duty
     */
    public function getIsOffDutyAttribute(): bool
    {
        if ((bool) $this->is_off) {
            return true;
        }

        if (empty($this->shift_id) || !$this->shift) {
            return true;
        }

        return (bool) ($this->shift->is_holiday ?? false);
    }

    /**
     * [BARU] Helper statis untuk mengambil jadwal pola kerja HARI INI (1 = Senin s/d 7 = Minggu)
     */
    public static function getTodaySchedule(): ?self
    {
        $todayIso = Carbon::now()->dayOfWeekIso;

        return static::with('shift')
            ->where('day_number', $todayIso)
            ->first();
    }
}