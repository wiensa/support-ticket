<?php

namespace Wiensa\SupportTicket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Attachment
 * 
 * @property string $id
 * @property string $attachable_id
 * @property string $attachable_type
 * @property string $file_name
 * @property string $file_path
 * @property string $file_type
 * @property int $file_size
 * @property string|null $user_id
 * @property string|null $user_type
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Model $attachable
 * @property-read \Illuminate\Database\Eloquent\Model|null $user
 */
class Attachment extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_attachments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'user_id',
        'user_type',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'file_size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the parent attachable model.
     */
    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the user that uploaded the attachment.
     */
    public function user(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the full URL of the attachment.
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }
} 