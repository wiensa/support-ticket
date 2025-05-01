<?php

namespace Wiensa\SupportTicket\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Validator;
use Wiensa\SupportTicket\Events\TicketReplied;

class TicketReplyController extends Controller
{
    /**
     * Yeni yanıt ekle
     */
    public function store(Request $request, Ticket $ticket)
    {
        $this->authorize('reply', $ticket);
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'is_private' => 'boolean',
            'attachments.*' => 'file|max:5120', // 5MB max
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $reply = new TicketReply();
        $reply->ticket_id = $ticket->id;
        $reply->message = $request->message;
        $reply->is_private = $request->has('is_private') ? (bool)$request->is_private : false;
        
        // Admin mi kontrol et
        $reply->is_admin = $request->user()->can('adminReply', $ticket);
        
        // Kullanıcı bilgilerini ekle
        $user = $request->user();
        $reply->user()->associate($user);
        
        $reply->save();
        
        // Dosya eklerini kaydet
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $reply->fileAttachments()->create([
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $file->store('ticket_attachments', 'public'),
                    'file_type' => $file->getMimeType(),
                    'file_size' => $file->getSize(),
                    'user_id' => $user->getKey(),
                    'user_type' => get_class($user),
                ]);
            }
        }
        
        // Talep durumunu güncelle (admin yanıtladıysa beklemede, kullanıcı yanıtladıysa açık)
        if ($reply->is_admin) {
            $ticket->status = Ticket::STATUS_PENDING;
        } else {
            // Kapalı bir talebe yanıt gelirse yeniden aç
            if ($ticket->isClosed() || $ticket->isResolved()) {
                $ticket->status = Ticket::STATUS_OPEN;
                $ticket->closed_at = null;
            }
        }
        
        $ticket->save();
        
        // Yanıt eklendi eventi fırlat
        event(new TicketReplied($ticket, $reply));
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', 'Yanıtınız başarıyla eklendi.');
    }
    
    /**
     * Yanıtı güncelle
     */
    public function update(Request $request, Ticket $ticket, TicketReply $reply)
    {
        $this->authorize('update', $reply);
        
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'is_private' => 'boolean',
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }
        
        $reply->message = $request->message;
        $reply->is_private = $request->has('is_private') ? (bool)$request->is_private : false;
        $reply->save();
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', 'Yanıt başarıyla güncellendi.');
    }
    
    /**
     * Yanıtı sil
     */
    public function destroy(Ticket $ticket, TicketReply $reply)
    {
        $this->authorize('delete', $reply);
        
        $reply->delete();
        
        return redirect()->route('supportticket.tickets.show', $ticket->id)
            ->with('success', 'Yanıt başarıyla silindi.');
    }
} 