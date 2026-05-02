<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Dashboard;

use Illuminate\Support\Facades\Route;

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
     *
     * @param string $host
     * @param int $port
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
     *
     * @return void
     */
    public function start(): void
    {
        $this->registerRoutes();
        
        // TODO: Start development server
    }

    /**
     * Register dashboard routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        Route::prefix('domo')
            ->name('domo.')
            ->middleware(config('domo.dashboard.middleware', ['web']))
            ->group(function () {
                Route::get('/', fn() => view('domo::dashboard.index'))->name('index');
                Route::get('/schema', fn() => view('domo::dashboard.schema'))->name('schema');
                Route::get('/models', fn() => view('domo::dashboard.models'))->name('models');
            });
    }

    /**
     * Get the server URL.
     *
     * @return string
     */
    public function getUrl(): string
    {
        return "http://{$this->host}:{$this->port}";
    }

    /**
     * Get the dashboard URL.
     *
     * @return string
     */
    public function getDashboardUrl(): string
    {
        return "{$this->getUrl()}/domo";
    }
}
