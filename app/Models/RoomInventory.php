<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomInventory extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'room_inventory';

    protected $fillable = [
        'room_id',
        'item_id',
        'expected_quantity',
        'current_quantity',
    ];

    protected $casts = [
        'expected_quantity' => 'integer',
        'current_quantity'  => 'integer',
    ];

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }

    // Helper: 
    public function hasMissing(): bool
    {
        return $this->current_quantity < $this->expected_quantity;
    }
}