<?php

namespace Wiensa\SupportTicket\Events;

use Wiensa\SupportTicket\Models\Attachment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AttachmentUploaded
{
    use Dispatchable, SerializesModels;

    /**
     * @var Attachment
     */
    public $attachment;

    /**
     * @var Model
     */
    public $attachableModel;

    /**
     * Create a new event instance.
     *
     * @param Attachment $attachment
     * @param Model $attachableModel
     * @return void
     */
    public function __construct(Attachment $attachment, Model $attachableModel)
    {
        $this->attachment = $attachment;
        $this->attachableModel = $attachableModel;
    }
} 