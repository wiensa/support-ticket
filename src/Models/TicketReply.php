<?php

namespace Wiensa\SupportTicket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class TicketReply
 * 
 * @property string $id
 * @property string $ticket_id
 * @property string $message
 * @property string|null $user_id
 * @property string|null $user_type
 * @property bool $is_admin
 * @property bool $is_private
 * @property array|null $attachments
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Wiensa\SupportTicket\Models\Ticket $ticket
 * @property-read \Illuminate\Database\Eloquent\Model|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Wiensa\SupportTicket\Models\Attachment> $fileAttachments
 */
class TicketReply extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_replies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'ticket_id',
        'message',
        'user_id',
        'user_type',
        'is_admin',
        'is_private',
        'attachments',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_admin' => 'boolean',
        'is_private' => 'boolean',
        'attachments' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the ticket that owns the reply.
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Get the user that owns the reply.
     */
    public function user(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Get the attachments for the reply.
     */
    public function fileAttachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }
} 