<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function hasInventory(){
        return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
    }
}
