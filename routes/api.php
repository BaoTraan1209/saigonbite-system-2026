<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HubController;
use App\Http\Controllers\Api\CatalogController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\KitchenController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\Api\ReportController;

Route::get('/hubs', [HubController::class, 'index']);
Route::get('/hubs/{hub}', [HubController::class, 'show']);
Route::post('/hubs', [HubController::class, 'store']);
Route::put('/hubs/{hub}', [HubController::class, 'update']);
Route::patch('/hubs/{hub}/status', [HubController::class, 'changeStatus']);

Route::apiResource('/brands', CatalogController::class)->only(['index','store','show','update','destroy']);
Route::get('/brands/{brand}/items', [CatalogController::class, 'items']);
Route::post('/menu-items', [CatalogController::class, 'storeItem']);
Route::put('/menu-items/{menuItem}', [CatalogController::class, 'updateItem']);
Route::delete('/menu-items/{menuItem}', [CatalogController::class, 'deleteItem']);

Route::get('/orders', [OrderController::class, 'index']);
Route::post('/orders', [OrderController::class, 'store']);
Route::get('/orders/{order}', [OrderController::class, 'show']);
Route::get('/kitchen/orders', [KitchenController::class, 'index']);
Route::patch('/kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus']);
Route::get('/deliveries', [DeliveryController::class, 'index']);
Route::patch('/deliveries/{delivery}/status', [DeliveryController::class, 'updateStatus']);
Route::get('/reports/summary', [ReportController::class, 'summary']);
