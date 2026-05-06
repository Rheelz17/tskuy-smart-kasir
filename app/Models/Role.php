<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Kasih tau Laravel kolom mana aja yang boleh diisi
    protected $fillable = ['name'];

    /**
     * RELASI: Satu Role bisa dimiliki oleh banyak User.
     * Biar lu bisa manggil: $role->users
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}