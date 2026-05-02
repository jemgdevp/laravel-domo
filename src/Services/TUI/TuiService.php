<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use Laravel\Prompts\Prompt;
use Laravel\Prompts\Themes\Default\Renderer;

/**
 * Terminal UI Service for Laravel Domo.
 *
 * Provides interactive TUI components using Laravel Prompts.
 *
 * @see https://laravel.com/docs/11.x/prompts
 */
class TuiService
{
    /**
     * Render the main menu.
     *
     * @return string
     */
    public function renderMainMenu(): string
    {
        return Prompt::menu(
            label: 'Laravel Domo - Main Menu',
            options: [
                'schema' => '📊 View Database Schema',
                'models' => '🔧 View Eloquent Models',
                'analyze' => '🤖 AI Analysis',
                'migrations' => '📝 Manage Migrations',
                'export' => '📤 Export SQL',
                'quit' => '❌ Exit',
            ],
            default: 'schema',
            scroll: 5,
        );
    }

    /**
     * Render table selection menu.
     *
     * @param array<string> $tables
     * @return string
     */
    public function selectTable(array $tables): string
    {
        return Prompt::select(
            label: 'Select a table to inspect',
            options: $tables,
            scroll: 10,
        );
    }

    /**
     * Display schema information.
     *
     * @param string $table
     * @param array<string, mixed> $columns
     * @return void
     */
    public function displaySchema(string $table, array $columns): void
    {
        Prompt::note("Schema for table: {$table}");
        
        // TODO: Implement table display with Termwind
    }

    /**
     * Display loading spinner.
     *
     * @param string $label
     * @param callable $callback
     * @return mixed
     */
    public function withSpinner(string $label, callable $callback): mixed
    {
        return Prompt::spinner($label, $callback);
    }

    /**
     * Display success message.
     *
     * @param string $message
     * @return void
     */
    public function success(string $message): void
    {
        Prompt::note("✅ {$message}");
    }

    /**
     * Display error message.
     *
     * @param string $message
     * @return void
     */
    public function error(string $message): void
    {
        Prompt::error($message);
    }

    /**
     * Confirm action.
     *
     * @param string $label
     * @param bool $default
     * @return bool
     */
    public function confirm(string $label, bool $default = false): bool
    {
        return Prompt::confirm($label, $default);
    }

    /**
     * Get text input.
     *
     * @param string $label
     * @param string $default
     * @return string
     */
    public function text(string $label, string $default = ''): string
    {
        return Prompt::text($label, $default);
    }
}
