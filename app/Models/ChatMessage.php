<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatMessage extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'chat_id', 
        'sender_id', 
        'message', 
        'file_path', 
        'is_read'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_read'    => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke User (Pengirim Pesan).
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Relasi ke Chat (Wadah percakapan).
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }
    
    /**
     * Scope untuk mengambil pesan yang belum dibaca (untuk notifikasi).
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }
}