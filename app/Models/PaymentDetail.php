<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentDetail extends Model
{
    protected $table = 'payment_details';

    protected $fillable = ['payment_id', 'order_item_id', 'amount'];

    public $timestamps = false;

    protected $casts = [
        'amount' => 'decimal:2'
    ];

    // Relasi: Merujuk ke nota Pembayaran induk
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Relasi: Merujuk ke Item Pesanan yang dibayarkan
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }
}