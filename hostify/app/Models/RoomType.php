<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomType extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'name', 'description', 'base_price', 'capacity', 'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'capacity'   => 'integer',
        'is_active'  => 'boolean',
    ];

    //  Relaciones 

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    //  Scopes 

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}