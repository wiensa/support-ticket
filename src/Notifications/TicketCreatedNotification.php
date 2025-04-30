<?php

namespace Wiensa\SupportTicket\Notifications;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * The ticket instance.
     *
     * @var \Wiensa\SupportTicket\Models\Ticket
     */
    protected $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return config('supportticket.notifications.ticket_created.channels', ['mail']);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('supportticket::notifications.ticket_created_subject', ['id' => $this->ticket->id]))
            ->greeting(__('supportticket::notifications.greeting'))
            ->line(__('supportticket::notifications.ticket_created_line1'))
            ->line(__('supportticket::notifications.ticket_subject', ['subject' => $this->ticket->subject]))
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
            'message' => __('supportticket::notifications.ticket_created_database_message'),
        ];
    }
} 