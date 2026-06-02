<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $fillable = [
        'name', 'description', 'price', 'stock', 'category_id', 'image', 'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    //Relasi: Menu dimiliki oleh satu kategori
    public function category(): BelongsTo 
    {
        return $this->belongsTo(Category::class);
    }

    //Relasi Many to many ke moods melalui tabel pivot menu_moods
    public function moods(): BelongsToMany
    {
        return $this->belongsToMany(Mood::class, 'menu_mood', 'menu_id', 'mood_id');
    }

    //Relasi: Satu menu memiliki banak opsi varian (level gula, es, dll)
    public function options(): HasMany
    {
        return $this->hasMany(MenuOption::class);
    }
}
