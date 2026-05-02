<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Laravel\Prompts\Prompt;

/**
 * TUI Screen Manager.
 *
 * Handles navigation between different TUI screens.
 */
class ScreenManager
{
    /**
     * Create a new screen manager instance.
     *
     * @param TuiService $tui
     * @param SchemaAnalyzerInterface $analyzer
     */
    public function __construct(
        protected TuiService $tui,
        protected SchemaAnalyzerInterface $analyzer
    ) {
    }

    /**
     * Run the main TUI loop.
     *
     * @return void
     */
    public function run(): void
    {
        while (true) {
            $choice = $this->tui->renderMainMenu();

            match ($choice) {
                'schema' => $this->showSchemaScreen(),
                'models' => $this->showModelsScreen(),
                'analyze' => $this->showAnalysisScreen(),
                'migrations' => $this->showMigrationsScreen(),
                'export' => $this->showExportScreen(),
                'quit' => break,
                default => $this->tui->error('Invalid option'),
            };
        }

        $this->tui->success('Goodbye!');
    }

    /**
     * Show schema screen.
     *
     * @return void
     */
    protected function showSchemaScreen(): void
    {
        $tables = $this->analyzer->getTables();

        if (empty($tables)) {
            $this->tui->error('No tables found');
            return;
        }

        $selectedTable = $this->tui->selectTable(
            collect($tables)->map(fn($table) => is_array($table) ? reset($table) : $table)->toArray()
        );

        $schema = $this->analyzer->getTableSchema($selectedTable);
        $this->tui->displaySchema($selectedTable, $schema['columns'] ?? []);
    }

    /**
     * Show models screen.
     *
     * @return void
     */
    protected function showModelsScreen(): void
    {
        $models = $this->analyzer->getModels();

        if (empty($models)) {
            $this->tui->error('No models found');
            return;
        }

        $this->tui->success('Found ' . count($models) . ' models');
        
        // TODO: Display models with relationships
    }

    /**
     * Show AI analysis screen.
     *
     * @return void
     */
    protected function showAnalysisScreen(): void
    {
        $this->tui->withSpinner('Analyzing schema with AI...', function () {
            // TODO: Implement AI analysis
            sleep(1);
        });

        $this->tui->success('Analysis complete');
    }

    /**
     * Show migrations screen.
     *
     * @return void
     */
    protected function showMigrationsScreen(): void
    {
        $this->tui->success('Migrations screen - Coming soon');
    }

    /**
     * Show export screen.
     *
     * @return void
     */
    protected function showExportScreen(): void
    {
        $this->tui->success('Export screen - Coming soon');
    }
}
