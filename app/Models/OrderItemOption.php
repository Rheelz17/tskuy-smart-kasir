<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemOption extends Model
{
    protected $table = 'order_item_options';

    protected $fillable = ['order_item_id', 'option_value_id'];

    public $timestamps = false;

    // Relasi: Pilihan opsi ini melekat pada Item Pesanan mana
    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class, 'order_item_id');
    }

    // Relasi: Mengambil detail nilai/harga tambahan dari master opsi menu
    public function optionValue(): BelongsTo
    {
        return $this->belongsTo(MenuOptionValue::class, 'option_value_id');
    }
}