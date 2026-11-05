<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['code', 'name', 'price_per_sqft', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}