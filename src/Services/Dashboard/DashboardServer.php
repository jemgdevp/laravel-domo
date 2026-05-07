<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Dashboard;

use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Http\Controllers\DashboardController;
use Symfony\Component\Process\Process;

use function Illuminate\Support\php_binary;

/**
 * Dashboard Server Service.
 *
 * Manages the web dashboard server for Laravel Domo.
 * Uses Laravel's built-in development server.
 */
class DashboardServer
{
    /**
     * The host address to bind to.
     */
    protected string $host;

    /**
     * The port to serve on.
     */
    protected int $port;

    /**
     * Create a new dashboard server instance.
     */
    public function __construct(
        string $host = '127.0.0.1',
        int $port = 8080
    ) {
        $this->host = $host;
        $this->port = $port;
    }

    /**
     * Start the dashboard server.
     */
    public function start(string $host, int $port, bool $block = true): int
    {
        $this->host = $host;
        $this->port = $port;

        $this->registerRoutes();

        $process = $this->buildProcess();
        $process->setTimeout(null);

        if (! $block) {
            $process->start();

            return 0;
        }

        return $process->run(function ($type, $buffer) {
            echo $buffer;
        });
    }

    /**
     * Register dashboard routes.
     */
    protected function registerRoutes(): void
    {
        Route::prefix(config('domo.dashboard.route', 'domo'))
            ->name('domo.')
            ->middleware(config('domo.dashboard.middleware', ['web']))
            ->group(function () {
                Route::get('/', [DashboardController::class, 'index'])->name('index');
                Route::get('/schema', [DashboardController::class, 'schema'])->name('schema');
                Route::get('/models', [DashboardController::class, 'models'])->name('models');
                Route::get('/analyze', [DashboardController::class, 'analyzePage'])->name('analyze');
                Route::post('/analyze', [DashboardController::class, 'analyze'])->name('analyze.post');
            });
    }

    /**
     * Get the server URL.
     */
    public function getUrl(): string
    {
        return "http://{$this->host}:{$this->port}";
    }

    /**
     * Get the dashboard URL.
     */
    public function getDashboardUrl(): string
    {
        $route = config('domo.dashboard.route', 'domo');

        return "{$this->getUrl()}/{$route}";
    }

    /**
     * Build the PHP development server process.
     */
    protected function buildProcess(): Process
    {
        return new Process([
            php_binary(),
            '-S',
            "{$this->host}:{$this->port}",
            $this->resolveServerScript(),
        ], public_path());
    }

    /**
     * Resolve the server script path.
     */
    protected function resolveServerScript(): string
    {
        $server = base_path('server.php');

        if (is_file($server)) {
            return $server;
        }

        $frameworkServer = base_path('vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

        if (is_file($frameworkServer)) {
            return $frameworkServer;
        }

        throw new \RuntimeException('Unable to locate Laravel server.php for the dashboard server.');
    }
}
