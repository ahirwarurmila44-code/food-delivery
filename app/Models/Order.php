<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
    'user_id',
    'total_price',
    'address',
    'payment_method',
    'status',
    'delivery_status',
    'tracking_number',
    'estimated_delivery',
];
}

