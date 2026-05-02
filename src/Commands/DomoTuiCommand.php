<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Commands;

use Illuminate\Console\Command;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\TUI\ScreenManager;
use Jemgdevp\Domo\Services\TUI\TuiService;

/**
 * Launch the Laravel Domo terminal user interface.
 *
 * Provides an interactive CLI workflow for database management
 * using Laravel Prompts for beautiful terminal UI.
 *
 * @example php artisan domo:tui
 */
class DomoTuiCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domo:tui
                            {--no-colors : Disable colors in TUI}
                            {--simple : Use simple theme}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Launch the Laravel Domo terminal user interface';

    /**
     * Execute the console command.
     */
    public function handle(
        TuiService $tui,
        ScreenManager $screenManager,
        SchemaAnalyzerInterface $analyzer
    ): int {
        $this->info('💻 Laravel Domo TUI');
        $this->line('');

        // Configure TUI based on options
        if ($this->option('no-colors')) {
            $this->warn('Colors disabled');
        }

        if ($this->option('simple')) {
            $this->warn('Using simple theme');
        }

        $this->line('Starting interactive terminal interface...');
        $this->line('');

        // Run TUI
        $screenManager->run();

        return Command::SUCCESS;
    }
}
