<?php

namespace Wiensa\SupportTicket;

use Wiensa\SupportTicket\Console\Commands\InstallSupportTicketCommand;
use Wiensa\SupportTicket\Events\TicketClosed;
use Wiensa\SupportTicket\Events\TicketCreated;
use Wiensa\SupportTicket\Events\TicketReplied;
use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Policies\TicketPolicy;
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
    }

    /**
     * Register any package services.
     */
    public function register(): void
    {
        $this->registerConfig();
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
    }
} 