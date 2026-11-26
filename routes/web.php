<?php
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/order');
Route::view('/order', 'start')->name('order.start');
Route::view('/order/menu', 'menu')->name('order.menu');
Route::view('/order/checkout', 'checkout')->name('order.checkout');
Route::view('/order/review', 'review')->name('order.review');
Route::view('/order/success', 'success')->name('order.success');
