<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\HubController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\ReportController;

// HUB ROUTES
Route::get('/hubs', [HubController::class, 'index']);
Route::get('/hubs/{hub}', [HubController::class, 'show']);
Route::post('/hubs', [HubController::class, 'store']);
Route::put('/hubs/{hub}', [HubController::class, 'update']);
Route::patch('/hubs/{hub}/status', [HubController::class, 'changeStatus']);


// BRAND ROUTES
Route::get('/brands', [CatalogController::class, 'index']);
Route::post('/brands', [CatalogController::class, 'store']);
Route::get('/brands/{brand}/items', [CatalogController::class, 'items']);
Route::get('/brands/{brand}', [CatalogController::class, 'show']);
Route::put('/brands/{brand}', [CatalogController::class, 'update']);
Route::delete('/brands/{brand}', [CatalogController::class, 'destroy']);


// MENU ITEM ROUTES
Route::post('/menu-items', [CatalogController::class, 'storeItem']);
Route::put('/menu-items/{menuItem}', [CatalogController::class, 'updateItem']);
Route::delete('/menu-items/{menuItem}', [CatalogController::class, 'deleteItem']);


// ORDER ROUTES
Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);

