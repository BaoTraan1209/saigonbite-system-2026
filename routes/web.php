<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => auth()->check()
    ? redirect()->route(auth()->user()->role === 'admin' ? 'admin.dashboard' : 'order.start')
    : response()->view('login'))->name('home');

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:5,1')->name('login.store');
Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::view('/order', 'start')->name('order.start');
    Route::view('/order/menu', 'menu')->name('order.menu');
    Route::view('/order/checkout', 'checkout')->name('order.checkout');
    Route::view('/order/review', 'review')->name('order.review');
    Route::view('/order/success', 'success')->name('order.success');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::view('/admin/catalog', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/admin/catalog/brands', 'admin.brands.index')->name('admin.brands.index');
    Route::view('/admin/catalog/brands/create', 'admin.brands.form')->name('admin.brands.create');
    Route::view('/admin/catalog/brands/{brand}/edit', 'admin.brands.form')->name('admin.brands.edit');
    Route::view('/admin/catalog/items', 'admin.items.index')->name('admin.items.index');
});
