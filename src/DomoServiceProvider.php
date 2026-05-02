<?php

declare(strict_types=1);

namespace Jemgdevp\Domo;

use Illuminate\Support\ServiceProvider;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\McpServerInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;
use Jemgdevp\Domo\Services\MCP\DomoMcpServer;
use Jemgdevp\Domo\Services\Schema\Analyzer;

class DomoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/domo.php', 'domo');

        $this->app->singleton(SchemaAnalyzerInterface::class, Analyzer::class);
        $this->app->singleton(McpServerInterface::class, DomoMcpServer::class);

        // Register AI driver based on config
        $driver = config('domo.ai_driver', 'openai');
        $this->registerAiDriver($driver);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register AI driver.
     *
     * @param string $driver
     * @return void
     */
    protected function registerAiDriver(string $driver): void
    {
        $this->app->bind(AiDriverInterface::class, match ($driver) {
            'anthropic' => AnthropicDriver::class,
            'openai' => OpenAIDriver::class,
            default => OpenAIDriver::class,
        });
    }

    /**
     * Register routes.
     *
     * @return void
     */
    protected function registerRoutes(): void
    {
        if ($this->app->runningInConsole()) {
            return;
        }

        $this->loadRoutesFrom(__DIR__ . '/Http/Routes/web.php');
    }

    /**
     * Register commands.
     *
     * @return void
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\DomoServeCommand::class,
                Commands\DomoTuiCommand::class,
            ]);
        }
    }

    /**
     * Register publishing.
     *
     * @return void
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__ . '/../config/domo.php' => config_path('domo.php'),
        ], 'domo-config');

        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/domo'),
        ], 'domo-views');
    }
}
