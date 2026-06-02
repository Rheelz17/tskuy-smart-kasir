<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = ['table_number', 'status'];

    //Relasi: Satu meja bisa digunakan untuk banyak pesanan 
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'table_id');
    }
}
