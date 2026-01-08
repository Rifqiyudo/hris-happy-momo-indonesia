<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang boleh diisi massal
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'position_id', // Relasi ke Jabatan
        'nik',
        'phone',
        'address',
        'bank_name',
        'bank_account',
        'role',     // 'admin' or 'employee'
        'is_active',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // --- RELASI (RELATIONSHIPS) ---

    // 1 User punya 1 Jabatan
    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }

    // 1 User punya BANYAK Absensi
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    // 1 User punya BANYAK History Gaji
    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    // --- HELPER METHODS ---
    
    // Cek apakah user adalah Admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Ambil foto profil atau gambar default
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }
}