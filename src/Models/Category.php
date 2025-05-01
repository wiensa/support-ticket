<?php

namespace Wiensa\SupportTicket\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Class Category
 * 
 * @property string $id
 * @property string $name
 * @property string $slug
 * @property string|null $color
 * @property string|null $icon
 * @property string|null $description
 * @property bool $is_active
 * @property int $sort_order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * 
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Wiensa\SupportTicket\Models\Ticket> $tickets
 */
class Category extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'support_ticket_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'color',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the tickets for the category.
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'category', 'id');
    }
} 