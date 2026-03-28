<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomInventory extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'room_id',
        'item_id',
        'current_quantity',
    ];

    protected $casts = [
        'current_quantity' => 'integer',
    ];

    //  Relaciones 

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    //  Helpers 

    public function isBelowMinStock(): bool
    {
        return $this->current_quantity < $this->item->min_stock;
    }
}