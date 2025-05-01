<?php

namespace Wiensa\SupportTicket\Services;

use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Support\Facades\Auth;
use Wiensa\SupportTicket\Events\TicketCreated;
use Wiensa\SupportTicket\Events\TicketReplied;
use Wiensa\SupportTicket\Events\TicketClosed;
use Wiensa\SupportTicket\Events\TicketReopened;
use Wiensa\SupportTicket\Events\TicketStatusChanged;
use Illuminate\Database\Eloquent\Model;

class TicketService
{
    /**
     * The attachment service instance.
     *
     * @var \Wiensa\SupportTicket\Services\AttachmentService
     */
    protected $attachmentService;

    /**
     * Create a new service instance.
     *
     * @param \Wiensa\SupportTicket\Services\AttachmentService $attachmentService
     * @return void
     */
    public function __construct(AttachmentService $attachmentService)
    {
        $this->attachmentService = $attachmentService;
    }

    /**
     * Yeni bir destek talebi oluştur
     *
     * @param array $data
     * @param Model|null $user
     * @return Ticket
     */
    public function createTicket(array $data, ?Model $user = null): Ticket
    {
        // Talep oluştur
        $ticket = new Ticket();
        $ticket->subject = $data['subject'];
        $ticket->message = $data['message'];
        $ticket->category = $data['category'] ?? null;
        $ticket->priority = $data['priority'] ?? Ticket::PRIORITY_MEDIUM;
        $ticket->status = Ticket::STATUS_OPEN;
        
        // Kullanıcı bilgilerini ayarla
        if ($user) {
            $ticket->user()->associate($user);
        } elseif (Auth::check()) {
            $ticket->user()->associate(Auth::user());
        }
        
        $ticket->save();
        
        // Dosya ekleri
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $this->attachmentService->uploadFile($file, $ticket, $user);
            }
        }
        
        // Event'i config'e göre tetikle
        if (config('supportticket.events.ticket_created', true)) {
            event(new TicketCreated($ticket));
        }
        
        return $ticket;
    }
    
    /**
     * Destek talebine yanıt ekle
     *
     * @param Ticket $ticket
     * @param array $data
     * @param Model|null $user
     * @param bool $isAdmin
     * @return TicketReply
     */
    public function addReply(Ticket $ticket, array $data, ?Model $user = null, bool $isAdmin = false): TicketReply
    {
        // Yanıt oluştur
        $reply = new TicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->message = $data['message'];
        $reply->is_private = $data['is_private'] ?? false;
        $reply->is_admin = $isAdmin;
        
        // Kullanıcı bilgilerini ayarla
        if ($user) {
            $reply->user()->associate($user);
        } elseif (Auth::check()) {
            $reply->user()->associate(Auth::user());
        }
        
        $reply->save();
        
        // Dosya ekleri
        if (isset($data['attachments']) && is_array($data['attachments'])) {
            foreach ($data['attachments'] as $file) {
                $this->attachmentService->uploadFile($file, $reply, $user);
            }
        }
        
        // Talep durumunu güncelle
        $this->updateTicketStatus($ticket, $isAdmin);
        
        // Event'i config'e göre tetikle
        if (config('supportticket.events.ticket_replied', true)) {
            event(new TicketReplied($ticket, $reply));
        }
        
        return $reply;
    }
    
    /**
     * Destek talebinin durumunu güncelle
     *
     * @param Ticket $ticket
     * @param bool $isAdminReply
     * @return Ticket
     */
    public function updateTicketStatus(Ticket $ticket, bool $isAdminReply = false): Ticket
    {
        $oldStatus = $ticket->status;
        
        // Eğer admin yanıtladıysa, talebi beklemede durumuna getir
        if ($isAdminReply) {
            $ticket->status = Ticket::STATUS_PENDING;
        } 
        // Eğer kullanıcı yanıtladıysa ve talep kapalı veya çözülmüş ise, talebi yeniden aç
        else if ($ticket->isClosed() || $ticket->isResolved()) {
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->closed_at = null;
        }
        
        $ticket->save();
        
        // Durum değişmişse event'i config'e göre tetikle
        if ($oldStatus !== $ticket->status && config('supportticket.events.ticket_status_changed', true)) {
            event(new TicketStatusChanged($ticket, $oldStatus));
        }
        
        return $ticket;
    }
    
    /**
     * Destek talebini kapat
     *
     * @param Ticket $ticket
     * @param string|null $note
     * @param Model|null $user
     * @return Ticket
     */
    public function closeTicket(Ticket $ticket, ?string $note = null, ?Model $user = null): Ticket
    {
        if (!$ticket->isClosed()) {
            $oldStatus = $ticket->status;
            $ticket->status = Ticket::STATUS_CLOSED;
            $ticket->closed_at = now();
            $ticket->save();
            
            // Kapanış notu ekle
            if ($note) {
                $data = [
                    'message' => $note,
                    'is_private' => false,
                ];
                
                $this->addReply($ticket, $data, $user, true);
            }
            
            // Event'i config'e göre tetikle
            if (config('supportticket.events.ticket_closed', true)) {
                event(new TicketClosed($ticket));
            }
            
            // Durum değişmişse event'i config'e göre tetikle
            if ($oldStatus !== $ticket->status && config('supportticket.events.ticket_status_changed', true)) {
                event(new TicketStatusChanged($ticket, $oldStatus));
            }
        }
        
        return $ticket;
    }
    
    /**
     * Destek talebini yeniden aç
     *
     * @param Ticket $ticket
     * @param string|null $note
     * @param Model|null $user
     * @return Ticket
     */
    public function reopenTicket(Ticket $ticket, ?string $note = null, ?Model $user = null): Ticket
    {
        if ($ticket->isClosed() || $ticket->isResolved()) {
            $oldStatus = $ticket->status;
            $ticket->status = Ticket::STATUS_OPEN;
            $ticket->closed_at = null;
            $ticket->save();
            
            // Yeniden açılış notu ekle
            if ($note) {
                $data = [
                    'message' => $note,
                    'is_private' => false,
                ];
                
                $this->addReply($ticket, $data, $user, true);
            }
            
            // Event'i config'e göre tetikle
            if (config('supportticket.events.ticket_reopened', true)) {
                event(new TicketReopened($ticket));
            }
            
            // Durum değişmişse event'i config'e göre tetikle
            if ($oldStatus !== $ticket->status && config('supportticket.events.ticket_status_changed', true)) {
                event(new TicketStatusChanged($ticket, $oldStatus));
            }
        }
        
        return $ticket;
    }
} 