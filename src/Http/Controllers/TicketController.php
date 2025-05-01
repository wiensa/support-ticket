<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Wiensa\SupportTicket\Http\Requests\CreateTicketRequest;
use Wiensa\SupportTicket\Http\Requests\ReplyTicketRequest;
use Wiensa\SupportTicket\Http\Requests\UpdateTicketRequest;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Wiensa\SupportTicket\Models\Category;
use Wiensa\SupportTicket\Services\TicketService;
use Wiensa\SupportTicket\Services\AttachmentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Wiensa\SupportTicket\Events\TicketCreated;
use Wiensa\SupportTicket\Events\TicketClosed;

class TicketController extends Controller
{
    /**
     * The ticket service instance.
     *
     * @var \Wiensa\SupportTicket\Services\TicketService
     */
    protected $ticketService;

    /**
     * The attachment service instance.
     *
     * @var \Wiensa\SupportTicket\Services\AttachmentService
     */
    protected $attachmentService;

    /**
     * Create a new controller instance.
     *
     * @param \Wiensa\SupportTicket\Services\TicketService $ticketService
     * @param \Wiensa\SupportTicket\Services\AttachmentService $attachmentService
     * @return void
     */
    public function __construct(TicketService $ticketService, AttachmentService $attachmentService)
    {
        $this->ticketService = $ticketService;
        $this->attachmentService = $attachmentService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Ticket::class);

        $query = Ticket::query();
        
        // Filtreler
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->has('category')) {
            $query->where('category', $request->category);
        }
        
        if ($request->has('priority')) {
            $query->where('priority', $request->priority);
        }
        
        // Kullanıcıya ait talepleri filtrele
        if (!$request->user()->can('viewAny', Ticket::class)) {
            $query->where(function($q) use ($request) {
                $q->where('user_id', $request->user()->getKey())
                  ->where('user_type', get_class($request->user()));
            });
        }
        
        $tickets = $query->latest()->paginate(15);
        $categories = Category::where('is_active', true)->get();
        
        return view('supportticket::tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->authorize('create', Ticket::class);

        $categories = Category::where('is_active', true)->get();
        return view('supportticket::tickets.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTicketRequest $request): RedirectResponse
    {
        $data = $request->validated();
        
        // Dosya eklerini ekle
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments');
        }
        
        // Ticket service ile talebi oluştur
        $ticket = $this->ticketService->createTicket($data, $request->user());
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', __('supportticket::messages.ticket_created'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket): View
    {
        $this->authorize('view', $ticket);

        $ticket->load(['replies', 'categoryRelation', 'attachments']);

        return view('supportticket::tickets.show', compact('ticket'));
    }

    /**
     * Reply to the specified ticket.
     */
    public function reply(ReplyTicketRequest $request, Ticket $ticket): RedirectResponse
    {
        $data = $request->validated();
        
        // Dosya eklerini ekle
        if ($request->hasFile('attachments')) {
            $data['attachments'] = $request->file('attachments');
        }
        
        // Ticket service ile yanıt ekle
        $reply = $this->ticketService->addReply($ticket, $data, $request->user(), false);
        
        return redirect()
            ->route('supportticket.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.reply_added'));
    }

    /**
     * Close the specified ticket.
     */
    public function close(Request $request, Ticket $ticket): RedirectResponse
    {
        $this->authorize('close', $ticket);

        $note = $request->input('note');
        
        // Ticket service ile talebi kapat
        $this->ticketService->closeTicket($ticket, $note, $request->user());

        return redirect()
            ->route('supportticket.tickets.show', $ticket)
            ->with('success', __('supportticket::messages.ticket_closed'));
    }

    /**
     * Destek talebini güncelleme formu
     */
    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        $categories = Category::where('is_active', true)->get();
        
        return view('supportticket::tickets.edit', compact('ticket', 'categories'));
    }
    
    /**
     * Destek talebini güncelle
     */
    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $data = $request->validated();
        
        $oldStatus = $ticket->status;
        
        $ticket->fill($data);
        
        // Talebi kapatırsa closed_at'i ayarla
        if ($data['status'] === Ticket::STATUS_CLOSED && $oldStatus !== Ticket::STATUS_CLOSED) {
            $ticket->closed_at = now();
        }
        
        $ticket->save();
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', __('supportticket::messages.ticket_updated'));
    }
    
    /**
     * Destek talebini sil
     */
    public function destroy(Ticket $ticket)
    {
        $this->authorize('delete', $ticket);
        
        $ticket->delete();
        
        return redirect()->route('supportticket.tickets.index')
            ->with('success', __('supportticket::messages.ticket_deleted'));
    }
    
    /**
     * Kapalı destek talebini yeniden aç
     */
    public function reopen(Request $request, Ticket $ticket)
    {
        $this->authorize('update', $ticket);
        
        $note = $request->input('note');
        
        // Ticket service ile talebi yeniden aç
        $this->ticketService->reopenTicket($ticket, $note, $request->user());
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', __('supportticket::messages.ticket_reopened'));
    }
} 