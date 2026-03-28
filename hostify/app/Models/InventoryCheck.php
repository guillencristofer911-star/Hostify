<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheck extends Model
{
    use HasUuids;

    protected $fillable = [
        'cleaning_session_id',
        'item_id',
        'quantity_found',
    ];

    protected $casts = [
        'quantity_found' => 'integer',
    ];

    //  Relaciones 

    public function cleaningSession(): BelongsTo
    {
        return $this->belongsTo(CleaningSession::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(InventoryItem::class, 'item_id');
    }
}