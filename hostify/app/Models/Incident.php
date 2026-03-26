<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Incident extends Model {
    use HasUuids;
    protected $fillable = [
        'room_id','reservation_id','cleaning_session_id','reported_by',
        'category','status','description','photo_url','resolved_at','resolved_by'
    ];
    protected $casts = ['resolved_at' => 'datetime'];
    public function room(): BelongsTo { return $this->belongsTo(Room::class); }
    public function reservation(): BelongsTo { return $this->belongsTo(Reservation::class); }
    public function cleaningSession(): BelongsTo { return $this->belongsTo(CleaningSession::class); }
    public function reportedBy(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
    public function resolvedBy(): BelongsTo { return $this->belongsTo(User::class, 'resolved_by'); }
    public function scopePending($q) { return $q->where('status', 'pendiente'); }
}
