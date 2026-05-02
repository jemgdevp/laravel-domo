<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\error;
use function Laravel\Prompts\menu;
use function Laravel\Prompts\note;
use function Laravel\Prompts\select;
use function Laravel\Prompts\spinner;
use function Laravel\Prompts\text;

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
     */
    public function renderMainMenu(): string
    {
        return menu(
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
     * @param  array<string>  $tables
     */
    public function selectTable(array $tables): string
    {
        return select(
            label: 'Select a table to inspect',
            options: $tables,
            scroll: 10,
        );
    }

    /**
     * Display schema information.
     *
     * @param  array<string, mixed>  $columns
     */
    public function displaySchema(string $table, array $columns): void
    {
        note("Schema for table: {$table}");

        // TODO: Implement table display with Termwind
    }

    /**
     * Display loading spinner.
     */
    public function withSpinner(string $label, callable $callback): mixed
    {
        return spinner($label, $callback);
    }

    /**
     * Display success message.
     */
    public function success(string $message): void
    {
        note("✅ {$message}");
    }

    /**
     * Display error message.
     */
    public function error(string $message): void
    {
        error($message);
    }

    /**
     * Confirm action.
     */
    public function confirm(string $label, bool $default = false): bool
    {
        return confirm($label, $default);
    }

    /**
     * Get text input.
     */
    public function text(string $label, string $default = ''): string
    {
        return text($label, $default);
    }
}
