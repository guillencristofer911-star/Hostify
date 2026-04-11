<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'category',
        'description',
        'unit',
        'min_quantity',
        'is_active',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'min_quantity' => 'integer',
    ];

    public function roomInventories(): HasMany
    {
        return $this->hasMany(RoomInventory::class, 'item_id');
    }

    public function checks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class, 'item_id');
    }
}