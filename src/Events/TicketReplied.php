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
     * @var Ticket
     */
    public $ticket;

    /**
     * @var TicketReply
     */
    public $reply;

    /**
     * Create a new event instance.
     *
     * @param Ticket $ticket
     * @param TicketReply $reply
     * @return void
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }
} 