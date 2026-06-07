<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_code', 
        'customer_name', 
        'table_id', 
        'order_type', 
        'source', 
        'status', 
        'is_open_bill', 
        'subtotal', 
        'tax', 
        'total', 
        'transaction_id',
        'snap_token',
        'created_by'
    ];

    protected $casts = [
        'is_open_bill' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    //Relasi: Pesanan tercatat di satu meja tertentu 
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class, 'table_id');
    }
    
    //Relasi: Pesanan dibuat oleh user karyawan (nullable jika self order)
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    //Relasi: Satu pesanan memiliki banyak item makanan/minuman yang dipesan
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    //Relasi: Satu pesanan bisa memuat beberapa kali transaksi pembayaran (cicil/split bill)
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    //Relasi: Rekam pembagian tagihan split bill per orang 
    public function billShares(): HasMany
    {
        return $this->hasMany(OrderBillShare::class, 'order_id');
    }

    //Relasi: Log aktivitas/perubahan status pesanan 
    public function logs(): HasMany
    {
        return $this->hasMany(OrderLog::class, 'order_id');
    }
}
