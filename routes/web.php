<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\CartController;
use App\Http\Controllers\Front\ProductController;
use App\Http\Controllers\Auth\UserAuthController;
use App\Http\Controllers\Front\RestaurantController;

// Route::get('/', function () {
//     return view('welcome');
// });

Auth::routes();

Route::get('register', [UserAuthController::class, 'showRegisterForm'])->name('user.register');
Route::post('register', [UserAuthController::class, 'register'])->name('user.register.submit');

Route::get('login', [UserAuthController::class, 'showLoginForm'])->name('user.login');
Route::post('login', [UserAuthController::class, 'login'])->name('user.login.submit');
Route::post('logout', [UserAuthController::class, 'logout'])->name('user.logout');
Route::middleware('auth')->group(function () {
    Route::get('dashboard', fn() => view('user.dashboard'))->name('user.dashboard');
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('/products', [ProductController::class, 'index'])->name('products.list');
     Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.list');
     Route::get('/restaurant/{id}/products', [RestaurantController::class, 'restaurantProducts'])->name('restaurant.products');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    //Route::get('dashboard', fn() => view('user.dashboard'))->name('user.dashboard');
    //Route::get('orders', [UserOrderController::class, 'index'])->name('user.orders');
    // Route::get('restaurants', [RestaurantController::class, 'index'])->name('user.restaurants');
    // Route::get('products', [ProductController::class, 'index'])->name('user.products');

    Route::prefix('cart')->group(function () {
        Route::post('/add', [CartController::class, 'add'])->middleware('auth')->name('cart.add');
        Route::get('/', [CartController::class, 'index']);
         Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
        Route::get('/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
        Route::post('/checkout/place-order', [CartController::class, 'placeOrder'])->name('cart.placeOrder');
    });

});

/////////////////////////food delivery cart


require __DIR__.'/admin.php';
