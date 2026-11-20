<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Announcement extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal.
     * Diselaraskan dengan kolom status dan department_id untuk filter cerdas.
     */
    protected $fillable = [
        'user_id',       // ID Pembuat (Admin/HRD)
        'department_id', // Target Divisi (Null = Global)
        'title', 
        'slug',          
        'content', 
        'type',          // info, warning, danger, success
        'target',        // all, employee, pic
        'status',        // active, inactive
        'is_active'      // Flag boolean untuk kompatibilitas
    ];

    /**
     * Casting atribut.
     */
    protected $casts = [
        'is_active'  => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    /**
     * Boot function untuk otomatisasi slug dan sinkronisasi status.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($announcement) {
            // Otomatis buat slug jika kosong
            if (empty($announcement->slug)) {
                $announcement->slug = Str::slug($announcement->title);
            }
            
            // Sinkronisasi status string 'active' dengan is_active boolean
            if (isset($announcement->is_active) && empty($announcement->status)) {
                $announcement->status = $announcement->is_active ? 'active' : 'inactive';
            }
        });
    }

    /**
     * Relasi ke Departemen.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id')
                    ->withDefault(['name' => 'Semua Divisi']);
    }

    /**
     * Relasi ke Author (User yang membuat pengumuman).
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')
                    ->withDefault(['name' => 'HRD Sistem']);
    }

    /**
     * ACCESSOR: Warna UI Tailwind berdasarkan tipe pengumuman.
     */
    public function getColorClassAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'amber',
            'danger'  => 'rose',
            'info'    => 'indigo',
            'success' => 'emerald',
            default   => 'blue',
        };
    }

    /**
     * ACCESSOR: Ikon FontAwesome berdasarkan tipe pengumuman.
     */
    public function getIconClassAttribute(): string
    {
        return match ($this->type) {
            'warning' => 'fa-exclamation-triangle',
            'danger'  => 'fa-bullhorn',
            'info'    => 'fa-info-circle',
            'success' => 'fa-check-circle',
            default   => 'fa-bell',
        };
    }

    /**
     * ACCESSOR: Format Waktu "Time Ago" (Contoh: 2 jam yang lalu).
     */
    public function getTimeAgoAttribute(): string
    {
        return Carbon::parse($this->created_at)->diffForHumans();
    }

    /**
     * SCOPE: Hanya ambil yang berstatus aktif (Boolean atau String).
     */
    public function scopeActive($query)
    {
        return $query->where(function($q) {
            $q->where('is_active', true)
              ->orWhere('status', 'active');
        });
    }

    /**
     * SCOPE: FILTER CERDAS (LOGIKA UTAMA)
     * Mengambil pengumuman yang sesuai dengan Jabatan (Role) dan Departemen user.
     */
    public function scopeForUser($query, $user)
    {
        return $query->active()
            ->where(function($q) use ($user) {
                /**
                 * 1. FILTER BERDASARKAN TARGET (ROLE)
                 * Jika Admin set target 'pic', maka staff biasa tidak bisa lihat.
                 * Jika Admin set target 'all', semua bisa lihat.
                 */
                $q->where('target', 'all')
                  ->orWhere('target', $user->role);
            })
            ->where(function($q) use ($user) {
                /**
                 * 2. FILTER BERDASARKAN DEPARTEMEN
                 * Muncul jika pengumuman bersifat global (department_id is NULL)
                 * ATAU department_id cocok dengan ID departemen user yang login.
                 */
                $q->whereNull('department_id')
                  ->orWhere('department_id', $user->department_id);
            });
    }
}