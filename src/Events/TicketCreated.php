<?php

namespace Wiensa\SupportTicket\Events;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketCreated
{
    use Dispatchable, SerializesModels;

    /**
     * @var Ticket
     */
    public $ticket;

    /**
     * Create a new event instance.
     *
     * @param Ticket $ticket
     * @return void
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }
} 