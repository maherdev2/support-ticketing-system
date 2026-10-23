<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TicketReply extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'message',
        'is_internal',
        'is_system',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
        'is_system' => 'boolean',
    ];

    /**
     * Get the ticket this reply belongs to
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user who wrote this reply
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all attachments for this reply
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Scope for public replies only
     */
    public function scopePublic($query)
    {
        return $query->where('is_internal', false)->where('is_system', false);
    }

    /**
     * Scope for internal notes only
     */
    public function scopeInternal($query)
    {
        return $query->where('is_internal', true);
    }

    /**
     * Scope for system messages only
     */
    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }
}
