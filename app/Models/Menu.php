<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'image',
        'is_available',   // Pengganti is_active
        'is_recommended', // Kolom baru
        'is_new',         // Kolom baru
        'is_promo'        // Kolom baru
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'is_available'   => 'boolean',
        'is_recommended' => 'boolean',
        'is_new'         => 'boolean',
        'is_promo'       => 'boolean',
    ];

    //Relasi: Menu dimiliki oleh satu kategori
    public function category(): BelongsTo 
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    // Relasi: Satu menu memiliki banyak item pesanan
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'menu_id');
    }

        // Relasi: Satu menu memiliki banyak grup opsi (level pedas, es, gula, dll)
    public function options(): HasMany
    {
        return $this->hasMany(MenuOption::class, 'menu_id');
    }

    public function moods(): BelongsToMany
    {
        return $this->belongsToMany(Mood::class, 'menu_moods', 'menu_id', 'mood_id');
    }
}