<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    // Pastikan kolom ini sesuai dengan database
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * Relasi: Satu Departemen memiliki BANYAK Karyawan (User)
     * Ini yang menyebabkan error "Call to undefined method" sebelumnya.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * (Opsional) Relasi: Satu Departemen memiliki BANYAK Jabatan (Position)
     * Jika Anda ingin menghitung jumlah jabatan juga.
     */
    public function positions(): HasMany
    {
        return $this->hasMany(Position::class);
    }
}