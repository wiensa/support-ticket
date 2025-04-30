<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Wiensa\SupportTicket\Http\Requests\ReplyTicketRequest;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class AdminTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $status = $request->get('status');
        
        $query = Ticket::query()->latest();
        
        if ($status && in_array($status, [
            Ticket::STATUS_OPEN, 
            Ticket::STATUS_PENDING, 
            Ticket::STATUS_RESOLVED, 
            Ticket::STATUS_CLOSED
        ])) {
            $query->where('status', $status);
        }
        
        $tickets = $query->paginate(20);

        return view('supportticket::admin.tickets.index', compact('tickets', 'status'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $replies = $ticket->replies()->latest()->get();

        return view('supportticket::admin.tickets.show', compact('ticket', 'replies'));
    }

    /**
     * Reply to the specified ticket.
     */
    public function reply(ReplyTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $reply = new TicketReply($request->validated());
        $reply->is_admin = true;
        $reply->user()->associate($request->user());
        
        $ticket->replies()->save($reply);

        // Update ticket status if provided
        if ($request->has('status') && in_array($request->status, [
            Ticket::STATUS_OPEN,
            Ticket::STATUS_PENDING,
            Ticket::STATUS_RESOLVED,
            Ticket::STATUS_CLOSED
        ])) {
            $oldStatus = $ticket->status;
            $ticket->status = $request->status;
            $ticket->save();
            
            // If ticket was closed, dispatch the closed event
            if ($ticket->status === Ticket::STATUS_CLOSED && $oldStatus !== Ticket::STATUS_CLOSED) {
                if (config('supportticket.events.ticket_closed', true)) {
                    event(new \Wiensa\SupportTicket\Events\TicketClosed($ticket));
                }
            }
        }

        // Dispatch ticket replied event if enabled
        if (config('supportticket.events.ticket_replied', true)) {
            event(new \Wiensa\SupportTicket\Events\TicketReplied($ticket, $reply));
        }

        return redirect()
            ->route('supportticket.admin.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.reply_added'));
    }

    /**
     * Update the status of the specified ticket.
     */
    public function updateStatus(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('reply', $ticket);

        $request->validate([
            'status' => ['required', 'string', 'in:open,pending,resolved,closed'],
        ]);

        $oldStatus = $ticket->status;
        $ticket->status = $request->status;
        $ticket->save();
        
        // If ticket was closed, dispatch the closed event
        if ($ticket->status === Ticket::STATUS_CLOSED && $oldStatus !== Ticket::STATUS_CLOSED) {
            if (config('supportticket.events.ticket_closed', true)) {
                event(new \Wiensa\SupportTicket\Events\TicketClosed($ticket));
            }
        }

        return redirect()
            ->route('supportticket.admin.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.status_updated'));
    }
} 