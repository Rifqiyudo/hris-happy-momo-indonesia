<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'file_path',
        'file_name',
        'file_size'
    ];

    /**
     * Relasi ke model User (Karyawan)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor untuk mendapatkan URL download aman
     */
    public function getDownloadUrlAttribute()
    {
        return route('admin.master.user.document.download', $this->id);
    }
}