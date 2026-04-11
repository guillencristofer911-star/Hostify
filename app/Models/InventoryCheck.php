<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheck extends Model
{
    use HasUuids;

    protected $fillable = [
        'cleaning_session_id',
        'item_id',
        'expected_quantity',
        'quantity_found',
        'is_ok',
        'notes',
    ];

    protected $casts = [
        'is_ok'             => 'boolean',
        'expected_quantity' => 'integer',
        'quantity_found'    => 'integer',
    ];

    public function cleaningSession(): BelongsTo
    {
        return $this->belongsTo(CleaningSession::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}