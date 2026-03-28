<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryItem extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'unit',
        'min_stock',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'min_stock' => 'integer',
    ];

    //  Relaciones 

    public function roomInventory(): HasMany
    {
        return $this->hasMany(RoomInventory::class, 'item_id');
    }

    public function inventoryChecks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class, 'item_id');
    }

    //  Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}