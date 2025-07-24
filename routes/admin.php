<?php

use App\Http\Controllers\Admin\AdminController as AdminAuth;
use App\Http\Controllers\Admin\RestaurantController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\OrderController;
use App\Jobs\SendInvoiceJob;

Route::get('/run-job', function () {
    dispatch(new TestJob());
    return 'Job dispatched';
});

Route::get('/test-email', function () {
    $order = \App\Models\Admin\Order::with('customer', 'items.product')->latest()->first();
    \Mail::to($order->customer->email)->send(new \App\Mail\InvoiceMail($order));
    return 'Sent!';
});
///////
Route::get('/admin/login', [AdminAuth::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuth::class, 'login'])->name('login');;

Route::prefix('admin')->middleware('auth:admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminAuth::class, 'dashboard'])->middleware('auth:admin')->name('dashboard');
    Route::post('/logout', [AdminAuth::class, 'logout'])->name('logout');

    // 
    Route::resource('restaurants', RestaurantController::class)->except('create', 'edit');

    //
    Route::resource('products', ProductController::class)->except('create', 'edit');
    Route::post('/products/toggle-available', [ProductController::class, 'toggleAvailable'])
     ->name('products.toggle.available');


    //
     Route::resource('categories', CategoryController::class)->except('create', 'edit');

     //
    Route::resource('customers', CustomerController::class)->except('create', 'edit');

    //
    Route::resource('orders', OrderController::class)->except('create', 'edit', 'update');
    Route::get('orders/{order}/view', [OrderController::class, 'view'])->name('orders.view');
    Route::get('orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
    Route::get('orders/{order}/send-invoice', [OrderController::class, 'sendInvoice'])->name('orders.send-invoice');
    Route::post('orders/{order}/status', [OrderController::class, 'updateStatus']);

});
