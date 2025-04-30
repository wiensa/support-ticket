<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Routes
    |--------------------------------------------------------------------------
    |
    | Here you can configure the route settings for your support ticket system.
    | 
    */
    'routes' => [
        'prefix' => 'support',
        'middleware' => ['web', 'auth'],
        'name' => 'supportticket.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Admin Routes
    |--------------------------------------------------------------------------
    |
    | Here you can configure the admin route settings for your support ticket system.
    | 
    */
    'admin_routes' => [
        'prefix' => 'admin/support',
        'middleware' => ['web', 'auth'],
        'name' => 'supportticket.admin.',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Permissions
    |--------------------------------------------------------------------------
    |
    | Here you can configure the permissions for your support ticket system.
    | 
    */
    'permissions' => [
        'view_tickets' => 'view-support-tickets',
        'create_tickets' => 'create-support-tickets',
        'reply_tickets' => 'reply-to-support-tickets',
        'close_tickets' => 'close-support-tickets',
        
        // Admin permissions
        'admin_view_all_tickets' => 'view-all-support-tickets',
        'admin_reply_tickets' => 'admin-reply-to-support-tickets',
        'admin_close_tickets' => 'admin-close-support-tickets',
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Notifications
    |--------------------------------------------------------------------------
    |
    | Here you can configure the notification settings for your support ticket system.
    | 
    */
    'notifications' => [
        'ticket_created' => [
            'enabled' => true,
            'channels' => ['mail', 'database'],
        ],
        'ticket_replied' => [
            'enabled' => true,
            'channels' => ['mail', 'database'],
        ],
        'ticket_closed' => [
            'enabled' => true,
            'channels' => ['mail', 'database'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Support Ticket Events
    |--------------------------------------------------------------------------
    |
    | Here you can configure the event settings for your support ticket system.
    | 
    */
    'events' => [
        'ticket_created' => true,
        'ticket_replied' => true,
        'ticket_closed' => true,
    ],
]; 