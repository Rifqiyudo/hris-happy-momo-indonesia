<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Chat extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 
        'hrd_id', 
        'subject', 
        'status'
    ];

    /**
     * Relasi ke pesan-pesan dalam chat ini.
     * Menggunakan 'created_at' agar urutan pesan tetap konsisten.
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class)->orderBy('created_at', 'asc');
    }

    /**
     * Relasi ke User (Karyawan) yang memulai chat.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke User (HRD) yang menangani chat.
     */
    public function hrd(): BelongsTo
    {
        return $this->belongsTo(User::class, 'hrd_id');
    }

    /**
     * Helper untuk mendapatkan pesan terakhir.
     * 'latestOfMany' sangat efisien untuk preview di dashboard/list.
     */
    public function lastMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latestOfMany();
    }

    /**
     * Helper untuk menghitung pesan yang belum dibaca.
     * Berguna untuk notifikasi di dashboard atau sidebar admin.
     */
    public function unreadCount($userId): int
    {
        return $this->messages()
                    ->where('is_read', false)
                    ->where('sender_id', '!=', $userId)
                    ->count();
    }
}