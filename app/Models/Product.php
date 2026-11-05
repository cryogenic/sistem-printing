<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Benarkan lajur ini diisi melalui borang
    protected $fillable = [
        'name', 
        'pricing_type', 
        'base_price', 
        'is_active'
    ];
}