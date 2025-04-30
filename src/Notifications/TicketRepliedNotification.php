<?php

namespace Wiensa\SupportTicket\Notifications;

use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRepliedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ticket instance.
     *
     * @var \Wiensa\SupportTicket\Models\Ticket
     */
    protected $ticket;

    /**
     * The ticket reply instance.
     *
     * @var \Wiensa\SupportTicket\Models\TicketReply
     */
    protected $reply;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, TicketReply $reply)
    {
        $this->ticket = $ticket;
        $this->reply = $reply;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return config('supportticket.notifications.ticket_replied.channels', ['mail']);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $title = $this->reply->is_admin 
            ? __('supportticket::notifications.admin_replied_title') 
            : __('supportticket::notifications.user_replied_title');

        return (new MailMessage)
            ->subject(__('supportticket::notifications.ticket_replied_subject', ['id' => $this->ticket->id]))
            ->greeting(__('supportticket::notifications.greeting'))
            ->line($title)
            ->line(__('supportticket::notifications.ticket_subject', ['subject' => $this->ticket->subject]))
            ->line(__('supportticket::notifications.reply_preview', ['message' => \Illuminate\Support\Str::limit($this->reply->message, 100)]))
            ->action(
                __('supportticket::notifications.view_ticket'),
                route('supportticket.tickets.show', $this->ticket)
            )
            ->line(__('supportticket::notifications.thank_you'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'subject' => $this->ticket->subject,
            'is_admin_reply' => $this->reply->is_admin,
            'message' => __('supportticket::notifications.ticket_replied_database_message'),
            'reply_preview' => \Illuminate\Support\Str::limit($this->reply->message, 100),
        ];
    }
} 