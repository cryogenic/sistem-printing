<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles; // Tambah baris ini

class User extends Authenticatable
{

    public function courierAddresses()
{
    return $this->hasMany(\App\Models\CourierAddress::class);
}

public function getAvatarUrlAttribute(): ?string
{
    return $this->avatar_path ? asset('storage/' . $this->avatar_path) : null;
}
    use HasFactory, Notifiable, HasRoles; // Masukkan HasRoles di sini

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone', // Tambahan medan telefon
        'agent_category_id', // Rujukan kategori ejen
    ];
    
    // ... kod sedia ada yang lain

    public function wallet()
    {
        return $this->hasOne(Wallet::class);
    }
}