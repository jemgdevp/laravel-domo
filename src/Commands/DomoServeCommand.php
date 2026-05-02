<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Commands;

use Illuminate\Console\Command;
use Jemgdevp\Domo\Services\Dashboard\DashboardServer;

/**
 * Start the Laravel Domo web dashboard server.
 *
 * Provides a visual web interface for database schema management,
 * model analysis, and AI-powered suggestions.
 *
 * @example php artisan domo:serve
 * @example php artisan domo:serve --host=0.0.0.0 --port=3000
 */
class DomoServeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domo:serve
                            {--host=127.0.0.1 : The host address to bind to}
                            {--port=8080 : The port to serve on}
                            {--open : Open the dashboard in your browser}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel Domo web dashboard server';

    /**
     * Execute the console command.
     */
    public function handle(DashboardServer $server): int
    {
        $host = $this->option('host');
        $port = $this->option('port');
        $open = $this->option('open');

        $this->info('🏠 Starting Laravel Domo Dashboard...');
        $this->line('');
        $this->line("  ➜  Local:   http://{$host}:{$port}/domo");
        $this->line('  ➜  Network: http://'.gethostbyname(gethostname()).":{$port}/domo");
        $this->line('');
        $this->line('  Press Ctrl+C to stop the server');
        $this->line('');

        if ($open) {
            $this->openInBrowser("http://{$host}:{$port}/domo");
        }

        // TODO: Start actual server using PHP built-in server
        // For now, show instructions
        $this->warn('Note: Full server implementation coming soon.');
        $this->warn('For now, use: php artisan serve --host='.$host.' --port='.$port);

        return Command::SUCCESS;
    }

    /**
     * Open URL in browser.
     */
    protected function openInBrowser(string $url): void
    {
        $escapedUrl = escapeshellarg($url);

        match (PHP_OS_FAMILY) {
            'Darwin' => exec("open {$escapedUrl}"),
            'Windows' => exec("start '' {$escapedUrl}"),
            'Linux' => exec("xdg-open {$escapedUrl}"),
            default => null,
        };

        $this->info('🌐 Opening dashboard in browser...');
    }
}
