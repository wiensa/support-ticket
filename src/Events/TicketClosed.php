<?php

namespace Wiensa\SupportTicket\Events;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketClosed
{
    use Dispatchable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var \Wiensa\SupportTicket\Models\Ticket
     */
    public $ticket;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
} 