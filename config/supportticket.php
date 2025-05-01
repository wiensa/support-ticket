<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Default Routes
    |--------------------------------------------------------------------------
    |
    | Configure the default routes for support tickets.
    |
    */
    'route_prefix' => 'support',
    'api_prefix' => 'api/support',
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Middleware
    |--------------------------------------------------------------------------
    |
    | Configure the middleware for support tickets routes.
    |
    */
    'middleware' => ['web', 'auth'],
    'admin_middleware' => ['web', 'auth'], // Admin middleware, add your admin middleware here
    'api_middleware' => ['api', 'auth:api'], // API middleware
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Admin Settings
    |--------------------------------------------------------------------------
    |
    | Configure admin settings for support tickets.
    |
    */
    'admin_role' => 'admin', // Default admin role
    'admin_emails' => [
        // Add admin emails here to receive notifications
        // 'admin@example.com',
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Notifications
    |--------------------------------------------------------------------------
    |
    | Configure notifications for support tickets.
    |
    */
    'events' => [
        'auto_notify' => true, // Automatically send notifications for events
        'ticket_created' => true, // Send notifications when a ticket is created
        'ticket_replied' => true, // Send notifications when a ticket is replied to
        'ticket_closed' => true, // Send notifications when a ticket is closed
        'ticket_reopened' => true, // Send notifications when a ticket is reopened
        'status_changed' => true, // Send notifications when a ticket status changes
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket File Attachments
    |--------------------------------------------------------------------------
    |
    | Configure file attachment settings for support tickets.
    |
    */
    'attachments' => [
        'enabled' => true, // Enable file attachments
        'max_size' => 5, // Max file size in MB
        'allowed_types' => 'jpg,jpeg,png,pdf,doc,docx,xls,xlsx,zip,txt', // Allowed file types
        'max_files' => 5, // Max number of files per ticket or reply
        'storage_disk' => 'public', // Storage disk to use (public, local, s3, etc.)
        'storage_path' => 'ticket_attachments', // Storage path for attachments
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Categories
    |--------------------------------------------------------------------------
    |
    | Configure category settings for support tickets.
    |
    */
    'categories' => [
        'enabled' => true, // Enable categories
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket User Model
    |--------------------------------------------------------------------------
    |
    | The user model that will be used for relationships.
    | By default, it uses the model defined in auth.providers.users.model.
    |
    */
    'user_model' => null, // If null, will use the model defined in auth.providers.users.model
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Pagination
    |--------------------------------------------------------------------------
    |
    | Configure pagination for support tickets.
    |
    */
    'pagination' => [
        'per_page' => 15, // Number of tickets per page
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Auto-Close
    |--------------------------------------------------------------------------
    |
    | Configure auto-close for support tickets.
    |
    */
    'auto_close' => [
        'enabled' => true, // Enable auto-close
        'days' => 7, // Number of days after which a ticket will be auto-closed
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Support Ticket Views
    |--------------------------------------------------------------------------
    |
    | Configure views for support tickets.
    |
    */
    'views' => [
        'layout' => 'supportticket::layouts.app', // Main layout for support ticket views
    ],
]; 