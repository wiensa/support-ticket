<?php

namespace Wiensa\SupportTicket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Ticket
 * 
 * @property string $id
 * @property string $subject
 * @property string $message
 * @property string $status
 * @property string $priority
 * @property string|null $category
 * @property string|null $assigned_to
 * @property string|null $user_id
 * @property string|null $user_type
 * @property \Illuminate\Support\Carbon|null $closed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Model|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Wiensa\SupportTicket\Models\TicketReply> $replies
 * @property-read \Wiensa\SupportTicket\Models\Category|null $categoryRelation
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Wiensa\SupportTicket\Models\Attachment> $attachments
 */
class Ticket extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_tickets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subject',
        'message',
        'status',
        'priority',
        'category',
        'assigned_to',
        'user_id',
        'user_type',
        'closed_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'closed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Ticket statuses
     */
    public const STATUS_OPEN = 'open';
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    /**
     * Ticket priorities
     */
    public const PRIORITY_LOW = 'low';
    public const PRIORITY_MEDIUM = 'medium';
    public const PRIORITY_HIGH = 'high';
    public const PRIORITY_URGENT = 'urgent';

    /**
     * Get the user that owns the ticket.
     */
    public function user(): BelongsTo
    {
        return $this->morphTo();
    }

    /**
     * Get the replies for the ticket.
     */
    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class);
    }

    /**
     * Get the category of the ticket.
     */
    public function categoryRelation(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category');
    }

    /**
     * Get the attachments for the ticket.
     */
    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    /**
     * Check if the ticket is open.
     */
    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    /**
     * Check if the ticket is pending.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Check if the ticket is resolved.
     */
    public function isResolved(): bool
    {
        return $this->status === self::STATUS_RESOLVED;
    }

    /**
     * Check if the ticket is closed.
     */
    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }
} 