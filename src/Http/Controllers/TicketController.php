<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Wiensa\SupportTicket\Http\Requests\CreateTicketRequest;
use Wiensa\SupportTicket\Http\Requests\ReplyTicketRequest;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $tickets = Ticket::query()
            ->where('user_id', $request->user()->getKey())
            ->where('user_type', get_class($request->user()))
            ->latest()
            ->paginate(10);

        return view('supportticket::tickets.index', compact('tickets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        return view('supportticket::tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request): RedirectResponse
    {
        $ticket = new Ticket($request->validated());
        $ticket->status = Ticket::STATUS_OPEN;
        $ticket->user()->associate($request->user());
        $ticket->save();

        // Dispatch ticket created event if enabled
        if (config('supportticket.events.ticket_created', true)) {
            event(new \Wiensa\SupportTicket\Events\TicketCreated($ticket));
        }

        return redirect()
            ->route('supportticket.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.ticket_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $replies = $ticket->replies()->latest()->get();

        return view('supportticket::tickets.show', compact('ticket', 'replies'));
    }

    /**
     * Reply to the specified ticket.
     */
    public function reply(ReplyTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $reply = new TicketReply($request->validated());
        $reply->is_admin = false;
        $reply->user()->associate($request->user());
        
        $ticket->replies()->save($reply);

        // If ticket was previously closed, reopen it
        if ($ticket->isClosed()) {
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->save();
        }

        // Dispatch ticket replied event if enabled
        if (config('supportticket.events.ticket_replied', true)) {
            event(new \Wiensa\SupportTicket\Events\TicketReplied($ticket, $reply));
        }

        return redirect()
            ->route('supportticket.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.reply_added'));
    }

    /**
     * Close the specified ticket.
     */
    public function close(Ticket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $ticket->status = Ticket::STATUS_CLOSED;
        $ticket->save();

        // Dispatch ticket closed event if enabled
        if (config('supportticket.events.ticket_closed', true)) {
            event(new \Wiensa\SupportTicket\Events\TicketClosed($ticket));
        }

        return redirect()
            ->route('supportticket.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.ticket_closed'));
    }
} 