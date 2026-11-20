<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkingPattern extends Model
{
    use HasFactory;

    /**
     * Nama tabel yang digunakan oleh model ini.
     * (Opsional jika penamaan standar, tapi baik untuk penegasan)
     */
    protected $table = 'working_patterns';

    /**
     * Kolom yang diizinkan untuk diisi massal.
     */
    protected $fillable = [
        'name',
        'mon_shift_id',
        'tue_shift_id',
        'wed_shift_id',
        'thu_shift_id',
        'fri_shift_id',
        'sat_shift_id',
        'sun_shift_id',
    ];

    // =========================================================================
    // RELASI KE TABEL SHIFTS (UNTUK SETIAP HARI KERJA)
    // =========================================================================

    public function shiftMonday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'mon_shift_id');
    }

    public function shiftTuesday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'tue_shift_id');
    }

    public function shiftWednesday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'wed_shift_id');
    }

    public function shiftThursday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'thu_shift_id');
    }

    public function shiftFriday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'fri_shift_id');
    }

    public function shiftSaturday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'sat_shift_id');
    }

    public function shiftSunday(): BelongsTo
    {
        return $this->belongsTo(Shift::class, 'sun_shift_id');
    }

    // =========================================================================
    // RELASI KE TABEL USERS (KARYAWAN)
    // =========================================================================

    /**
     * Relasi HasMany untuk mengetahui karyawan mana saja yang menggunakan
     * rujukan pola kerja mingguan ini.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'pattern_id');
    }
}