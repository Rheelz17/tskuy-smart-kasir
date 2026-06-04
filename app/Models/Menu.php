<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public function category(){
        return $this->belongsTo(Category::class);
    }
    public function moods(){
        return $this->belongsToMany(Mood::class, 'menu_mood');
    }
}
