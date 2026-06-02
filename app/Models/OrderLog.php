<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLog extends Model
{
    protected $table = 'order_logs';

    const UPDATED_AT = null; 

    protected $fillable = ['order_id', 'action', 'description', 'created_by'];

    // Relasi: Log ini mencatat riwayat dari Pesanan mana
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relasi: Siapa aktor/User yang memicu log aksi ini (Koki/Kasir)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}