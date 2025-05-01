<?php

namespace Wiensa\SupportTicket\Services;

use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Wiensa\SupportTicket\Models\Setting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Yeni bir destek talebi oluşturulduğunda bildirim gönder
     *
     * @param Ticket $ticket
     * @return void
     */
    public function sendNewTicketNotifications(Ticket $ticket): void
    {
        // Ayarlardan kontrol et
        if (!$this->isNotificationEnabled('notification_new_ticket')) {
            return;
        }
        
        // Admin kullanıcıları bul ve bildirim gönder
        $this->notifyAdmins('new_ticket', $ticket);
        
        // Talep sahibine bildirim
        $this->notifyTicketOwner('ticket_created', $ticket);
    }
    
    /**
     * Talebe yanıt eklendiğinde bildirim gönder
     *
     * @param Ticket $ticket
     * @param TicketReply $reply
     * @return void
     */
    public function sendTicketReplyNotifications(Ticket $ticket, TicketReply $reply): void
    {
        // Ayarlardan kontrol et
        if (!$this->isNotificationEnabled('notification_ticket_reply')) {
            return;
        }
        
        // Gizli yanıtları kontrol et
        if ($reply->is_private) {
            // Sadece adminlere bildirim
            $this->notifyAdmins('private_reply', $ticket, ['reply' => $reply]);
            return;
        }
        
        // Admin yanıtı ise kullanıcıya bildir
        if ($reply->is_admin) {
            $this->notifyTicketOwner('admin_replied', $ticket, ['reply' => $reply]);
        } 
        // Kullanıcı yanıtı ise adminlere bildir
        else {
            $this->notifyAdmins('user_replied', $ticket, ['reply' => $reply]);
        }
    }
    
    /**
     * Talep durumu değiştiğinde bildirim gönder
     *
     * @param Ticket $ticket
     * @param string $oldStatus
     * @return void
     */
    public function sendStatusChangeNotifications(Ticket $ticket, string $oldStatus): void
    {
        // Ayarlardan kontrol et
        if (!$this->isNotificationEnabled('notification_ticket_status_change')) {
            return;
        }
        
        // Durum değişikliği varsa bildirim gönder
        if ($ticket->status !== $oldStatus) {
            $data = ['old_status' => $oldStatus, 'new_status' => $ticket->status];
            
            // Talep sahibine bildirim
            $this->notifyTicketOwner('status_changed', $ticket, $data);
            
            // Adminlere bildirim
            $this->notifyAdmins('status_changed', $ticket, $data);
        }
    }
    
    /**
     * Talep kapatıldığında bildirim gönder
     *
     * @param Ticket $ticket
     * @return void
     */
    public function sendTicketClosedNotifications(Ticket $ticket): void
    {
        // Ayarlardan kontrol et
        if (!$this->isNotificationEnabled('notification_ticket_status_change')) {
            return;
        }
        
        // Talep sahibine bildirim
        $this->notifyTicketOwner('ticket_closed', $ticket);
    }
    
    /**
     * Admin kullanıcılara bildirim gönder
     *
     * @param string $type
     * @param Ticket $ticket
     * @param array $data
     * @return void
     */
    protected function notifyAdmins(string $type, Ticket $ticket, array $data = []): void
    {
        // Admin e-posta adreslerini al
        $adminEmails = config('supportticket.admin_emails', []);
        
        if (empty($adminEmails)) {
            return;
        }
        
        // E-posta şablonunu belirle
        $view = "supportticket::emails.admin.{$type}";
        $subject = $this->getNotificationSubject($type, $ticket);
        
        // Mailleri gönder
        foreach ($adminEmails as $email) {
            Mail::send($view, array_merge(['ticket' => $ticket], $data), function ($message) use ($email, $subject) {
                $message->to($email)->subject($subject);
            });
        }
    }
    
    /**
     * Talep sahibine bildirim gönder
     *
     * @param string $type
     * @param Ticket $ticket
     * @param array $data
     * @return void
     */
    protected function notifyTicketOwner(string $type, Ticket $ticket, array $data = []): void
    {
        // Kullanıcı yoksa çık
        if (!$ticket->user) {
            return;
        }
        
        // E-posta şablonunu belirle
        $view = "supportticket::emails.user.{$type}";
        $subject = $this->getNotificationSubject($type, $ticket);
        
        // Kullanıcı e-postasını al (model üzerinde email alanı olduğunu varsayıyoruz)
        $email = $ticket->user->email ?? null;
        
        if (!$email) {
            return;
        }
        
        // Mail gönder
        Mail::send($view, array_merge(['ticket' => $ticket], $data), function ($message) use ($email, $subject) {
            $message->to($email)->subject($subject);
        });
    }
    
    /**
     * Bildirim başlığını al
     *
     * @param string $type
     * @param Ticket $ticket
     * @return string
     */
    protected function getNotificationSubject(string $type, Ticket $ticket): string
    {
        $subjectMap = [
            'new_ticket' => 'Yeni Destek Talebi: ' . $ticket->subject,
            'ticket_created' => 'Destek Talebiniz Oluşturuldu: ' . $ticket->subject,
            'admin_replied' => 'Destek Talebinize Yanıt Verildi: ' . $ticket->subject,
            'user_replied' => 'Destek Talebine Yeni Yanıt: ' . $ticket->subject,
            'private_reply' => 'Özel Yanıt Eklendi: ' . $ticket->subject,
            'status_changed' => 'Destek Talebi Durumu Değişti: ' . $ticket->subject,
            'ticket_closed' => 'Destek Talebiniz Kapatıldı: ' . $ticket->subject,
        ];
        
        return $subjectMap[$type] ?? 'Destek Talebi: ' . $ticket->subject;
    }
    
    /**
     * Bildirim etkin mi kontrol et
     *
     * @param string $key
     * @return bool
     */
    protected function isNotificationEnabled(string $key): bool
    {
        // Setting tablosundan değeri kontrol et
        $value = Setting::getValue($key, 'true');
        
        return $value === 'true' || $value === '1' || $value === true;
    }
} 