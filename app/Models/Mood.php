<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mood extends Model
{
    protected $fillable = ['name'];
    
    public $timestamps = false; //tabel mood gapake created_at sama updated_at bang
    
// Relasi Many to Many ke menus melalui tabel pivot menu_moods
public function menus(): BelongsToMany {
    // ⬇️ Ganti 'menu_mood' menjadi 'menu_moods' (tambah huruf s)
    return $this->belongsToMany(Menu::class, 'menu_moods', 'mood_id', 'menu_id');
}
}