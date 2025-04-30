<?php

namespace Wiensa\SupportTicket\Events;

use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketReplied
{
    use Dispatchable, SerializesModels;

    /**
     * The ticket instance.
     *
     * @var \Wiensa\SupportTicket\Models\Ticket
     */
    public $ticket;

    /**
     * The ticket reply instance.
     *
     * @var \Wiensa\SupportTicket\Models\TicketReply
     */
    public $reply;

    /**
     * Create a new event instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }
} 