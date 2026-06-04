<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_method', 'total_amount', 'paid_amount', 
        'reference_number', 'status', 'paid_at'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'paid_at' => 'timestamp',
    ];

    // Relasi: Pembayaran ini ditujukan untuk melunasi Pesanan mana
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relasi: Rincian item-item belanja apa saja yang dibayar di transaksi ini (berguna saat split bill)
    public function details(): HasMany
    {
        return $this->hasMany(PaymentDetail::class, 'payment_id');
    }
}