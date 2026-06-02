<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrderItem extends Model
{
    protected $table = 'orders_item'; 

    protected $fillable = [
        'order_id', 'menu_id', 'quantity', 'price', 'note', 'status', 'rejected_reason'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    // Relasi: Item ini bagian dari sebuah Pesanan induk
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    // Relasi: Item ini merujuk ke Menu apa
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'menu_id');
    }

    // Relasi: Satu item menu yang dipesan bisa memiliki banyak opsi pilihan kustomisasi
    public function options(): HasMany
    {
        return $this->hasMany(OrderItemOption::class, 'order_item_id');
    }
}