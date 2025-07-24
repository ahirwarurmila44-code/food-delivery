<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    /** @use HasFactory<\Database\Factories\Admin\RestaurantFactory> */
    use HasFactory;

    protected $fillable = ['name', 'image', 'email', 'phone', 'address'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

}
