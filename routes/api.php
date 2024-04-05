<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InventoryItemController;
use Illuminate\Support\Facades\Route;

Route::post('inventory/list', [InventoryController::class, 'getAll']);
    Route::post('inventory/add', [InventoryController::class, 'create']);
    Route::post('inventory/update', [InventoryController::class, 'update']);
    Route::post('inventory/delete', [InventoryController::class, 'delete']);

    Route::post('item/list', [InventoryItemController::class, 'getAll']);
    Route::post('item-detail', [InventoryItemController::class, 'show']);
    Route::post('item/add', [InventoryItemController::class, 'create']);
    Route::post('item/update', [InventoryItemController::class, 'update']);
    Route::post('item/delete', [InventoryItemController::class, 'delete']);

Route::middleware('auth:api')->group(function (){

    // Route::get('inventory/list', [InventoryController::class, 'getAll']);
    // Route::post('inventory/add', [InventoryController::class, 'create']);
    // Route::post('inventory/update/{id}', [InventoryController::class, 'update']);
    // Route::delete('inventory/delete/{id}', [InventoryController::class, 'delete']);

    // Route::get('item/list/{inventoryId}', [InventoryItemController::class, 'getAll']);
    // Route::get('item/{id}', [InventoryItemController::class, 'show']);
    // Route::post('item/add', [InventoryItemController::class, 'create']);
    // Route::post('item/update/{id}', [InventoryItemController::class, 'update']);
    // Route::delete('item/delete/{id}', [InventoryItemController::class, 'delete']);
});
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
