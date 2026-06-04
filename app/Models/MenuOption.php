<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuOption extends Model
{
    protected $fillable = ['menu_id', 'name'];

    // Relasi: Opsi ini milik sebuah Menu
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    // Relasi: Satu grup opsi memiliki banyak nilai varian (misal: Less Sugar, Normal Sugar)
    public function values(): HasMany
    {
        return $this->hasMany(MenuOptionValue::class, 'option_id');
    }
}