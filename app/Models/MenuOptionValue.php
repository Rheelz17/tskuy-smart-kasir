<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuOptionValue extends Model
{
    protected $table = 'menu_option_values';

    protected $fillable = ['option_id', 'value', 'addtional_price'];

    protected $casts = [
        'additional_price' => 'decimal:2'
    ];

    //Relasi: Nilai varian ini merujuk pada grup opsi tertentu 
    public function option(): BelongsTo
    {
        return $this -> belongsTo(MenuOption::class, 'option_id');
    }
}
