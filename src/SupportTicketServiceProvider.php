<?php

namespace Wiensa\SupportTicket;

use Wiensa\SupportTicket\Console\Commands\InstallSupportTicketCommand;
use Wiensa\SupportTicket\Events\TicketClosed;
use Wiensa\SupportTicket\Events\TicketCreated;
use Wiensa\SupportTicket\Events\TicketReplied;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Models\TicketReply;
use Wiensa\SupportTicket\Models\Category;
use Wiensa\SupportTicket\Models\Setting;
use Wiensa\SupportTicket\Models\Attachment;
use Wiensa\SupportTicket\Policies\TicketPolicy;
use Wiensa\SupportTicket\Policies\TicketReplyPolicy;
use Wiensa\SupportTicket\Policies\CategoryPolicy;
use Wiensa\SupportTicket\Policies\SettingPolicy;
use Wiensa\SupportTicket\Policies\AttachmentPolicy;
use Wiensa\SupportTicket\Services\TicketService;
use Wiensa\SupportTicket\Services\AttachmentService;
use Wiensa\SupportTicket\Services\NotificationService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class SupportTicketServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any package services.
     */
    public function boot(): void
    {
        $this->registerPublishables();
        $this->loadResources();
        
        // Register commands
        if ($this->app->runningInConsole()) {
            $this->commands([
                InstallSupportTicketCommand::class,
            ]);
        }
        
        // Register policies
        $this->registerPolicies();
        
        // Event Listeners
        $this->registerEventListeners();
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->registerServices();
    }

    /**
     * Register the publishable resources
     */
    private function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__.'/../config/supportticket.php' => config_path('supportticket.php'),
            ], 'supportticket-config');

            // Publish migrations
            $this->publishes([
                __DIR__.'/../database/migrations/' => database_path('migrations'),
            ], 'supportticket-migrations');

            // Publish views
            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/supportticket'),
            ], 'supportticket-views');

            // Publish translations
            $this->publishes([
                __DIR__.'/../resources/lang' => lang_path('vendor/supportticket'),
            ], 'supportticket-lang');
            
            // Publish assets
            $this->publishes([
                __DIR__.'/../resources/assets' => public_path('vendor/supportticket'),
            ], 'supportticket-assets');
            
            // Publish seeders
            $this->publishes([
                __DIR__.'/../database/seeders/' => database_path('seeders'),
            ], 'supportticket-seeders');
        }
    }

    /**
     * Load package resources
     */
    private function loadResources(): void
    {
        // Load routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
        
        // Load views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'supportticket');
        
        // Load translations
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'supportticket');
        
        // Load migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    /**
     * Register package config
     */
    private function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/supportticket.php', 'supportticket');
    }
    
    /**
     * Register policies
     */
    private function registerPolicies(): void
    {
        // Register policies using Gate facade
        Gate::policy(Ticket::class, TicketPolicy::class);
        Gate::policy(TicketReply::class, TicketReplyPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Setting::class, SettingPolicy::class);
        Gate::policy(Attachment::class, AttachmentPolicy::class);
    }
    
    /**
     * Register services
     */
    private function registerServices(): void
    {
        // Register singleton services
        $this->app->singleton(TicketService::class, function ($app) {
            return new TicketService();
        });
        
        $this->app->singleton(AttachmentService::class, function ($app) {
            return new AttachmentService();
        });
        
        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService();
        });
        
        // Facade binding
        $this->app->bind('ticket', function ($app) {
            return $app->make(TicketService::class);
        });
        
        $this->app->bind('attachment', function ($app) {
            return $app->make(AttachmentService::class);
        });
        
        $this->app->bind('ticket.notification', function ($app) {
            return $app->make(NotificationService::class);
        });
    }
    
    /**
     * Register event listeners
     */
    private function registerEventListeners(): void
    {
        // Otomatik event ve listener binding
        if (config('supportticket.events.auto_notify', true)) {
            $this->app['events']->listen(TicketCreated::class, function (TicketCreated $event) {
                app(NotificationService::class)->sendNewTicketNotifications($event->ticket);
            });
            
            $this->app['events']->listen(TicketReplied::class, function (TicketReplied $event) {
                app(NotificationService::class)->sendTicketReplyNotifications($event->ticket, $event->reply);
            });
            
            $this->app['events']->listen(TicketClosed::class, function (TicketClosed $event) {
                app(NotificationService::class)->sendTicketClosedNotifications($event->ticket);
            });
        }
    }
} 