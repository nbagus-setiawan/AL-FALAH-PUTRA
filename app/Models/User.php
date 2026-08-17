<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Konstanta role — dipakai di middleware, policy, dan seeder
    // Menambah role baru di masa depan: cukup tambah konstanta + update middleware/route, tanpa migrasi skema
    public const ROLE_KETUA_UMUM = 'ketua_umum';
    public const ROLE_SEKRETARIS = 'sekretaris';
    public const ROLE_BENDAHARA = 'bendahara';

    protected $fillable = [
        'name', 'username', 'email', 'password', 'role', 'pengurus_id', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function pengurus()
    {
        return $this->belongsTo(Pengurus::class);
    }

    public function isKetuaUmum(): bool
    {
        return $this->role === self::ROLE_KETUA_UMUM;
    }

    public function isSekretaris(): bool
    {
        return $this->role === self::ROLE_SEKRETARIS;
    }

    public function isBendahara(): bool
    {
        return $this->role === self::ROLE_BENDAHARA;
    }

    // Hanya Sekretaris yang boleh melihat data kesehatan & NIK santri
    public function canViewDataSensitifSantri(): bool
    {
        return $this->isSekretaris();
    }
}
