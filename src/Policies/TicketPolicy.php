<?php

namespace Wiensa\SupportTicket\Policies;

use Wiensa\SupportTicket\Models\Ticket;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TicketPolicy
 * 
 * This policy governs ticket access and management.
 * It can be extended or overridden by the host application.
 */
class TicketPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any tickets.
     */
    public function viewAny($user): bool
    {
        return $this->checkPermission($user, config('supportticket.permissions.view_tickets')) || 
               $this->checkPermission($user, config('supportticket.permissions.admin_view_all_tickets'));
    }

    /**
     * Determine whether the user can view the ticket.
     */
    public function view($user, Ticket $ticket): bool
    {
        // Admin can view any ticket
        if ($this->checkPermission($user, config('supportticket.permissions.admin_view_all_tickets'))) {
            return true;
        }

        // User can view their own tickets
        if ($this->checkPermission($user, config('supportticket.permissions.view_tickets'))) {
            return $ticket->user_id === $user->getKey() && $ticket->user_type === get_class($user);
        }

        return false;
    }

    /**
     * Determine whether the user can create tickets.
     */
    public function create($user): bool
    {
        return $this->checkPermission($user, config('supportticket.permissions.create_tickets'));
    }

    /**
     * Determine whether the user can reply to the ticket.
     */
    public function reply($user, Ticket $ticket): bool
    {
        // Admin can reply to any ticket
        if ($this->checkPermission($user, config('supportticket.permissions.admin_reply_tickets'))) {
            return true;
        }

        // Users can reply to their own tickets if they have permission and the ticket is not closed
        if ($this->checkPermission($user, config('supportticket.permissions.reply_tickets'))) {
            return ($ticket->user_id === $user->getKey() && 
                    $ticket->user_type === get_class($user) &&
                    !$ticket->isClosed());
        }

        return false;
    }

    /**
     * Determine whether the user can close the ticket.
     */
    public function close($user, Ticket $ticket): bool
    {
        // Admin can close any ticket
        if ($this->checkPermission($user, config('supportticket.permissions.admin_close_tickets'))) {
            return true;
        }

        // Users can close their own tickets if they have permission
        if ($this->checkPermission($user, config('supportticket.permissions.close_tickets'))) {
            return $ticket->user_id === $user->getKey() && $ticket->user_type === get_class($user);
        }

        return false;
    }

    /**
     * Check if the user has the given permission.
     */
    protected function checkPermission($user, string $permission): bool
    {
        // If Laravel has permissions feature enabled
        if (method_exists($user, 'hasPermissionTo')) {
            return $user->hasPermissionTo($permission);
        }

        // If Laravel has a custom can method
        if (method_exists($user, 'can')) {
            return $user->can($permission);
        }

        // Default to true if no permission system is implemented
        return true;
    }
} 