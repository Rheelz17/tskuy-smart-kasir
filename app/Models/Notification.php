<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = ['user_id', 'title', 'message', 'reference_id', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    // Relasi: Notifikasi dikirimkan ke User mana (Koki/Kasir/Pelanggan)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi Opsional: Menghubungkan langsung reference_id ke order jika diperlukan
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'reference_id');
    }
}