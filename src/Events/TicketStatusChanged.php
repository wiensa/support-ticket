<?php

namespace Wiensa\SupportTicket\Events;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketStatusChanged
{
    use Dispatchable, SerializesModels;

    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * @var string
     */
    public $oldStatus;

    /**
     * Create a new event instance.
     *
     * @param Ticket $ticket
     * @param string $oldStatus
     * @return void
     */
    public function __construct(Ticket $ticket, string $oldStatus)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
    }
} 