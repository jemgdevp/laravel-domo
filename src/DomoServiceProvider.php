<?php

declare(strict_types=1);

namespace Jemgdevp\Domo;

use Illuminate\Support\ServiceProvider;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\McpServerInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\AI\AiDriverFactory;
use Jemgdevp\Domo\Services\MCP\DomoMcpServer;
use Jemgdevp\Domo\Services\Schema\Analyzer;
use Jemgdevp\Domo\Services\TUI\DomoTuiApp;
use Jemgdevp\Domo\Services\TUI\Screens\AnalyzeScreen;
use Jemgdevp\Domo\Services\TUI\Screens\ExportScreen;
use Jemgdevp\Domo\Services\TUI\Screens\HomeScreen;
use Jemgdevp\Domo\Services\TUI\Screens\MigrationsScreen;
use Jemgdevp\Domo\Services\TUI\Screens\ModelsScreen;
use Jemgdevp\Domo\Services\TUI\Screens\SchemaScreen;

class DomoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/domo.php', 'domo');

        $this->app->singleton(SchemaAnalyzerInterface::class, Analyzer::class);
        $this->app->singleton(McpServerInterface::class, DomoMcpServer::class);
        $this->app->singleton(DomoTuiApp::class, function ($app) {
            return new DomoTuiApp(
                screens: [
                    HomeScreen::class => $app->make(HomeScreen::class),
                    SchemaScreen::class => $app->make(SchemaScreen::class),
                    ModelsScreen::class => $app->make(ModelsScreen::class),
                    AnalyzeScreen::class => $app->make(AnalyzeScreen::class),
                    MigrationsScreen::class => $app->make(MigrationsScreen::class),
                    ExportScreen::class => $app->make(ExportScreen::class),
                ],
                initialScreen: HomeScreen::class,
            );
        });

        // Register the AI driver based on the active provider config.
        $this->registerAiDriver();
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerRoutes();
        $this->registerViews();
        $this->registerCommands();
        $this->registerPublishing();
    }

    /**
     * Register the package views under the "domo" namespace.
     *
     * Without this, the dashboard controller's view('domo::dashboard.*')
     * calls fail with "No hint path defined for [domo]".
     */
    protected function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'domo');
    }

    /**
     * Register the AI driver resolved from the active provider configuration.
     */
    protected function registerAiDriver(): void
    {
        $this->app->singleton(AiDriverFactory::class);

        $this->app->bind(
            AiDriverInterface::class,
            static fn ($app): AiDriverInterface => $app->make(AiDriverFactory::class)->make()
        );
    }

    /**
     * Register the dashboard routes.
     *
     * The dashboard auto-registers in the host application (no command
     * required) but, like Telescope, only in the configured environments
     * (default: "local") so it is never exposed in production.
     */
    protected function registerRoutes(): void
    {
        if (! $this->dashboardShouldRegister()) {
            return;
        }

        $this->loadRoutesFrom(__DIR__.'/Http/Routes/web.php');
    }

    /**
     * Determine whether the dashboard routes may register for the current
     * environment.
     */
    protected function dashboardShouldRegister(): bool
    {
        if (! config('domo.dashboard.enabled', true)) {
            return false;
        }

        $environments = config('domo.dashboard.environments', ['local']);
        $environments = is_array($environments) ? $environments : [$environments];

        // An empty list explicitly opts into every environment.
        if ($environments === []) {
            return true;
        }

        return $this->app->environment($environments);
    }

    /**
     * Register commands.
     */
    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\DomoServeCommand::class,
                Commands\DomoTuiCommand::class,
                Commands\DomoMcpCommand::class,
            ]);
        }
    }

    /**
     * Register publishing.
     */
    protected function registerPublishing(): void
    {
        $this->publishes([
            __DIR__.'/../config/domo.php' => config_path('domo.php'),
        ], 'domo-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/domo'),
        ], 'domo-views');
    }
}
