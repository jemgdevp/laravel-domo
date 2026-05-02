<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use Laravel\Prompts\MenuPrompt;
use Laravel\Prompts\SelectPrompt;
use Laravel\Prompts\NotePrompt;
use Laravel\Prompts\SpinnerPrompt;
use Laravel\Prompts\ErrorPrompt;
use Laravel\Prompts\ConfirmPrompt;
use Laravel\Prompts\TextPrompt;

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
        return MenuPrompt::make(
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
        )->prompt();
    }

    /**
     * Render table selection menu.
     *
     * @param  array<string>  $tables
     */
    public function selectTable(array $tables): string
    {
        return SelectPrompt::make(
            label: 'Select a table to inspect',
            options: $tables,
            scroll: 10,
        )->prompt();
    }

    /**
     * Display schema information.
     *
     * @param  array<string, mixed>  $columns
     */
    public function displaySchema(string $table, array $columns): void
    {
        NotePrompt::make("Schema for table: {$table}")->display();
    }

    /**
     * Display loading spinner.
     */
    public function withSpinner(string $label, callable $callback): mixed
    {
        return SpinnerPrompt::make(label: $label)->prompt($callback);
    }

    /**
     * Display success message.
     */
    public function success(string $message): void
    {
        NotePrompt::make("✅ {$message}")->display();
    }

    /**
     * Display error message.
     */
    public function error(string $message): void
    {
        ErrorPrompt::make($message)->display();
    }

    /**
     * Confirm action.
     */
    public function confirm(string $label, bool $default = false): bool
    {
        return ConfirmPrompt::make(label: $label, default: $default)->prompt();
    }

    /**
     * Get text input.
     */
    public function text(string $label, string $default = ''): string
    {
        return TextPrompt::make(label: $label, default: $default)->prompt();
    }
}
