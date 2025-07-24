<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\{Restaurant,Product};
use App\Models\Cart;

class RestaurantController extends Controller
{
    public function index()
    {
        $restaurants = Restaurant::orderBy('created_at', 'desc')->paginate(12);
        return view('frontend.restaurants.index', compact('restaurants'));
    }

    public function checkout()
    {
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        return view('frontend.restaurants.view', compact('cart'));
    }
    public function restaurantProducts($id)
    {
        $cart = Cart::where('user_id', auth()->id())->with('product')->get();
        $restaurant = Restaurant::findOrFail($id);
        $products = $restaurant->products()->latest()->get(); 
        
        return view('frontend.restaurants.view', compact('cart','restaurant', 'products'));
    }
}
