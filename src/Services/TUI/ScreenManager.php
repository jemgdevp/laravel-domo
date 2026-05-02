<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI;

use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;

/**
 * TUI Screen Manager.
 *
 * Handles navigation between different TUI screens.
 */
class ScreenManager
{
    /**
     * Create a new screen manager instance.
     */
    public function __construct(
        protected TuiService $tui,
        protected SchemaAnalyzerInterface $analyzer
    ) {}

    /**
     * Run the main TUI loop.
     */
    public function run(): void
    {
        while (true) {
            $choice = $this->tui->renderMainMenu();

            if ($choice === 'quit') {
                break;
            }

            match ($choice) {
                'schema' => $this->showSchemaScreen(),
                'models' => $this->showModelsScreen(),
                'analyze' => $this->showAnalysisScreen(),
                'migrations' => $this->showMigrationsScreen(),
                'export' => $this->showExportScreen(),
                default => $this->tui->error('Invalid option'),
            };
        }

        $this->tui->success('Goodbye!');
    }

    /**
     * Show schema screen.
     */
    protected function showSchemaScreen(): void
    {
        $tables = $this->analyzer->getTables();

        if (empty($tables)) {
            $this->tui->error('No tables found');

            return;
        }

        $options = collect($tables)->map(function ($table) {
            if (is_array($table)) {
                return reset($table);
            }

            if (is_object($table)) {
                $vars = get_object_vars($table);

                return reset($vars);
            }

            return $table;
        })->toArray();

        $selectedTable = $this->tui->selectTable($options);

        $schema = $this->analyzer->getTableSchema($selectedTable);
        $this->tui->displaySchema($selectedTable, $schema['columns'] ?? []);
    }

    /**
     * Show models screen.
     */
    protected function showModelsScreen(): void
    {
        $models = $this->analyzer->getModels();

        if (empty($models)) {
            $this->tui->error('No models found');

            return;
        }

        $this->tui->success('Found '.count($models).' models');

        // TODO: Display models with relationships
    }

    /**
     * Show AI analysis screen.
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
     */
    protected function showMigrationsScreen(): void
    {
        $this->tui->success('Migrations screen - Coming soon');
    }

    /**
     * Show export screen.
     */
    protected function showExportScreen(): void
    {
        $this->tui->success('Export screen - Coming soon');
    }
}
