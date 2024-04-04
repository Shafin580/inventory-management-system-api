<?php

use App\Http\Controllers\AuthController;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function (){

    Route::get('inventory/list', [Inventory::class, 'getAll']);
    Route::post('inventory/add', [Inventory::class, 'create']);
    Route::post('inventory/update/{id}', [Inventory::class, 'update']);
    Route::delete('inventory/delete/{id}', [Inventory::class, 'delete']);

    Route::get('item/list/{inventoryId}', [InventoryItem::class, 'getAll']);
    Route::get('item/{id}', [InventoryItem::class, 'show']);
    Route::post('item/add', [InventoryItem::class, 'create']);
    Route::post('item/update/{id}', [InventoryItem::class, 'update']);
    Route::delete('item/delete/{id}', [InventoryItem::class, 'delete']);
});
Route::post('auth/login', [AuthController::class, 'login']);
Route::post('auth/register', [AuthController::class, 'register']);
