<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'location_lat',
        'location_long',
        'photo_in',
        'photo_out',
        'late_minutes',
        'is_late',
        'status', // present, sick, permit, alpha
    ];

    // Agar clock_in dianggap sebagai Objek Waktu (Carbon), bukan string biasa
    protected $casts = [
        'date' => 'date',
        // 'clock_in' dan 'clock_out' di database tipe Time, 
        // Laravel otomatis membacanya sebagai string format 'H:i:s'
    ];

    // Relasi balik ke User
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Accessor: Status warna untuk View (Blade)
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'present' => 'bg-green-100 text-green-800',
            'sick' => 'bg-yellow-100 text-yellow-800',
            'alpha' => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}