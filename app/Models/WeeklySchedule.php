<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model WeeklySchedule
 * Mengelola template jadwal mingguan yang berlaku otomatis untuk seluruh karyawan.
 */
class WeeklySchedule extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     */
    protected $fillable = [
        'day_index', 
        'day_name', 
        'shift_id', 
        'is_off'
    ];

    /**
     * Casting tipe data agar konsisten saat diakses.
     */
    protected $casts = [
        'day_index' => 'integer',
        'is_off'    => 'boolean',
        'shift_id'  => 'integer',
    ];

    /**
     * Menambahkan atribut virtual ke dalam JSON/Array (opsional).
     */
    protected $appends = ['status_label'];

    // =========================================================================
    // RELASI (RELATIONSHIPS)
    // =========================================================================

    /**
     * Menghubungkan hari dengan Master Shift yang berlaku.
     */
    public function shift(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id')->withDefault([
            'name' => 'Tidak Ada Shift',
        ]);
    }

    // =========================================================================
    // SCOPES (QUERY HELPERS)
    // =========================================================================

    /**
     * Scope untuk mengambil jadwal urut dari Senin (1) ke Minggu (0).
     * Urutan ini umum digunakan dalam tampilan kalender/jadwal kerja Indonesia.
     */
    public function scopeOrdered($query)
    {
        return $query->orderByRaw("FIELD(day_index, 1, 2, 3, 4, 5, 6, 0)");
    }

    // =========================================================================
    // ACCESSORS (VIRTUAL ATTRIBUTES)
    // =========================================================================

    /**
     * Memberikan label status hari (Kerja/Libur) untuk tampilan Blade.
     */
    public function getStatusLabelAttribute(): string
    {
        return $this->is_off 
            ? '<span class="text-rose-500 font-black uppercase">Libur</span>' 
            : '<span class="text-emerald-500 font-black uppercase">Masuk Kerja</span>';
    }

    /**
     * Mengambil info jam kerja dalam format ringkas.
     */
    public function getWorkTimeAttribute(): string
    {
        if ($this->is_off || !$this->shift_id) {
            return '-';
        }

        return $this->shift->start_time . ' - ' . $this->shift->end_time;
    }

    // =========================================================================
    // HELPER METHODS (BUSINESS LOGIC)
    // =========================================================================

    /**
     * Mengecek apakah jadwal hari tertentu adalah hari kerja aktif.
     * Digunakan oleh sistem Absensi.
     */
    public static function isWorkDay(int $dayIndex): bool
    {
        $schedule = self::where('day_index', $dayIndex)->first();
        return $schedule && !$schedule->is_off && $schedule->shift_id;
    }
}