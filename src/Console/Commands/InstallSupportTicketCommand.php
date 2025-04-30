<?php

namespace Wiensa\SupportTicket\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class InstallSupportTicketCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supportticket:install {--force : Force publish all assets even if they already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Support Ticket System package resources';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Installing Support Ticket System...');

        $force = $this->option('force');

        // Publish config
        if ($this->publishConfig($force)) {
            $this->info('Published configuration');
        }

        // Publish migrations
        if ($this->publishMigrations($force)) {
            $this->info('Published migrations');
        }

        // Publish views
        if ($this->publishViews($force)) {
            $this->info('Published views');
        }

        // Publish language files
        if ($this->publishLanguageFiles($force)) {
            $this->info('Published language files');
        }

        // Create policy stub
        if ($this->createPolicyStub($force)) {
            $this->info('Created TicketPolicy stub in your application');
        }

        $this->info('Support Ticket System installed successfully.');

        return Command::SUCCESS;
    }

    /**
     * Publish the config files.
     */
    private function publishConfig(bool $force): bool
    {
        $params = [
            '--provider' => "Wiensa\SupportTicket\SupportTicketServiceProvider",
            '--tag' => "supportticket-config",
        ];

        if ($force) {
            $params['--force'] = true;
        }

        return $this->call('vendor:publish', $params) == 0;
    }

    /**
     * Publish the migrations.
     */
    private function publishMigrations(bool $force): bool
    {
        $params = [
            '--provider' => "Wiensa\SupportTicket\SupportTicketServiceProvider",
            '--tag' => "supportticket-migrations",
        ];

        if ($force) {
            $params['--force'] = true;
        }

        return $this->call('vendor:publish', $params) == 0;
    }

    /**
     * Publish the views.
     */
    private function publishViews(bool $force): bool
    {
        $params = [
            '--provider' => "Wiensa\SupportTicket\SupportTicketServiceProvider",
            '--tag' => "supportticket-views",
        ];

        if ($force) {
            $params['--force'] = true;
        }

        return $this->call('vendor:publish', $params) == 0;
    }

    /**
     * Publish the language files.
     */
    private function publishLanguageFiles(bool $force): bool
    {
        $params = [
            '--provider' => "Wiensa\SupportTicket\SupportTicketServiceProvider",
            '--tag' => "supportticket-lang",
        ];

        if ($force) {
            $params['--force'] = true;
        }

        return $this->call('vendor:publish', $params) == 0;
    }

    /**
     * Create policy stub in the application.
     */
    private function createPolicyStub(bool $force): bool
    {
        $policyPath = app_path('Policies/TicketPolicy.php');

        if (File::exists($policyPath) && !$force) {
            $this->warn('TicketPolicy already exists. Use --force to overwrite.');
            return false;
        }

        $stub = <<<EOT
<?php

namespace App\Policies;

use Wiensa\SupportTicket\Models\Ticket;
use Wiensa\SupportTicket\Policies\TicketPolicy as BaseTicketPolicy;

class TicketPolicy extends BaseTicketPolicy
{
    // Override package methods here or add your own
}
EOT;

        if (!File::isDirectory(app_path('Policies'))) {
            File::makeDirectory(app_path('Policies'), 0755, true);
        }

        File::put($policyPath, $stub);

        return true;
    }
} 