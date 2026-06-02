<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderBillShare extends Model
{
    protected $table = 'order_bill_shares';

    protected $fillable = ['order_id', 'user_id', 'computed_amount'];

    protected $casts = [
        'computed_amount' => 'decimal:2'
    ];

    // Relasi: Milik Pesanan yang mana
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relasi: Ditujukan beban tagihannya ke Akun User Pelanggan mana
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}