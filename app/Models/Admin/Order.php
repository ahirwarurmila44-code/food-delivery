<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_id', 'total_price', 'status','delivery_status', 'tracking_number', 'estimated_delivery'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
