<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryUserPivot extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'inventory_id',
    ];

    public function hasInventory(){
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }

    public function hasUser(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
