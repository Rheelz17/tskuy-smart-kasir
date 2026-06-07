<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuOption extends Model
{
    
    protected $table    = 'menu_options';
    protected $fillable = ['menu_id', 'name'];

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function values(): HasMany
    {
        return $this->hasMany(MenuOptionValue::class, 'option_id');
    }
}