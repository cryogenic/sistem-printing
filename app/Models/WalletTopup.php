<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTopup extends Model
{
    protected $fillable = ['user_id', 'amount', 'status', 'bill_code', 'payment_reference'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}