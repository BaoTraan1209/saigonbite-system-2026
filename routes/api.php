<?php

use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\HubController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\ReportController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->group(function () {
    Route::middleware('role:admin,customer')->group(function () {
        Route::get('/hubs', [HubController::class, 'index']);
        Route::get('/hubs/{hub}', [HubController::class, 'show']);
        Route::get('/brands', [CatalogController::class, 'index']);
        Route::get('/brands/{brand}/items', [CatalogController::class, 'items']);
        Route::get('/brands/{brand}', [CatalogController::class, 'show']);
        Route::get('/menu-items', [CatalogController::class, 'menuItems']);
        Route::get('/orders/{order}', [OrderController::class, 'show']);
    });

    Route::middleware('role:admin')->group(function () {
        Route::post('/hubs', [HubController::class, 'store']);
        Route::put('/hubs/{hub}', [HubController::class, 'update']);
        Route::patch('/hubs/{hub}/status', [HubController::class, 'changeStatus']);
        Route::put('/hubs/{hub}/brands', [HubController::class, 'syncBrands']);
        Route::post('/brands', [CatalogController::class, 'store']);
        Route::put('/brands/{brand}', [CatalogController::class, 'update']);
        Route::delete('/brands/{brand}', [CatalogController::class, 'destroy']);
        Route::post('/menu-items', [CatalogController::class, 'storeItem']);
        Route::put('/menu-items/{menuItem}', [CatalogController::class, 'updateItem']);
        Route::delete('/menu-items/{menuItem}', [CatalogController::class, 'deleteItem']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/kitchen/orders', [KitchenController::class, 'index']);
        Route::patch('/kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus']);
        Route::get('/deliveries', [DeliveryController::class, 'index']);
        Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus']);
        Route::get('/reports/summary', [ReportController::class, 'summary']);
    });

    Route::middleware('role:customer')->group(function () {
        Route::post('/orders', [OrderController::class, 'store']);
    });
});
