<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Kolom yang diizinkan untuk diisi secara massal (Mass Assignment).
     * Sesuaikan dengan struktur SQL Pro yang lu kirim tadi.
     */
    protected $fillable = [
        'employee_id',
        'name',
        'username',
        'email',
        'phone',
        'password',
        'photo',
        'role_id',
        'is_active',
        'last_login',
    ];

    /**
     * Data yang disembunyikan saat data user dipanggil (misal buat API).
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Casting tipe data agar Laravel otomatis ngebaca formatnya dengan benar.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean', // Biar dibaca true/false, bukan 1/0
            'last_login' => 'datetime',
        ];
    }

    /**
     * RELASI: User ini punya satu Role.
     * Biar lu bisa manggil: $user->role->name
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}