<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::where('available', 1)->latest()->take(8)->get();
        return view('frontend.home', compact('products'));
    }
}
