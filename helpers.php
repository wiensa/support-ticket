<?php

if (! function_exists('make')) {
    /**
     * @template TClass
     *
     * @param  class-string<TClass>  $abstract
     * @return TClass
     */
    function make(string $abstract, array $parameters = [])
    {
        return \Illuminate\Container\Container::getInstance()->make($abstract, $parameters);
    }
}

if (! function_exists('support_ticket')) {
    /**
     * Get a support ticket instance by ID.
     *
     * @param string|null $ticketId
     * @return \Wiensa\SupportTicket\Models\Ticket|null
     */
    function support_ticket(?string $ticketId = null)
    {
        if ($ticketId) {
            return \Wiensa\SupportTicket\Models\Ticket::find($ticketId);
        }
        
        return new \Wiensa\SupportTicket\Models\Ticket();
    }
}

if (! function_exists('ticket_status_badge')) {
    /**
     * Get HTML for a ticket status badge.
     *
     * @param string $status
     * @return string
     */
    function ticket_status_badge(string $status): string
    {
        $class = match ($status) {
            'open' => 'bg-success',
            'pending' => 'bg-warning',
            'resolved' => 'bg-info',
            'closed' => 'bg-secondary',
            default => 'bg-secondary',
        };

        return '<span class="badge ' . $class . '">' . 
               __('supportticket::tickets.status_' . $status) . 
               '</span>';
    }
}

if (! function_exists('support_ticket_count')) {
    /**
     * Get the count of tickets with a specific status.
     *
     * @param string|null $status
     * @param mixed|null $user
     * @return int
     */
    function support_ticket_count(?string $status = null, $user = null): int
    {
        $query = \Wiensa\SupportTicket\Models\Ticket::query();
        
        if ($status) {
            $query->where('status', $status);
        }
        
        if ($user) {
            $query->where('user_id', $user->getKey())
                  ->where('user_type', get_class($user));
        }
        
        return $query->count();
    }
}

if (! function_exists('ticket_route')) {
    /**
     * Generate a route for a ticket.
     *
     * @param string $name
     * @param mixed $parameters
     * @param bool $absolute
     * @param bool $isAdmin
     * @return string
     */
    function ticket_route(string $name, $parameters = [], bool $absolute = true, bool $isAdmin = false): string
    {
        $prefix = $isAdmin ? 'supportticket.admin.' : 'supportticket.';
        return route($prefix . $name, $parameters, $absolute);
    }
}
