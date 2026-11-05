<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
    'order_number',
    'job_title',
    'selected_material',   // <-- tambah
    'length',              // <-- tambah
    'width',               // <-- tambah
    'remarks',
    'fulfillment_type',
    'shipping_address',
    'user_id',
    'total_amount',
    'payment_method',
    'payment_status',
    'production_status',
    'artwork_path',
    'toyyibpay_bill_code',
    'payment_reference',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}