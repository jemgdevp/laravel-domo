<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Screens;

use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Stay;
use Jemgdevp\Domo\Services\TUI\Components\ChoiceFieldComponent;
use Jemgdevp\Domo\Services\TUI\Contracts\Action;
use Jemgdevp\Domo\Services\TUI\Contracts\Screen;
use Jemgdevp\Domo\Services\TUI\Widgets\BannerWidget;
use Jemgdevp\Domo\Services\TUI\Widgets\KeyHintsWidget;
use Jemgdevp\Domo\Services\TUI\Widgets\SchemaTableWidget;
use PhpTui\Tui\Extension\Core\Widget\BlockWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Text\Title;
use PhpTui\Tui\Widget\Borders;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Throwable;

class SchemaScreen implements Screen
{
    protected ChoiceFieldComponent $tablesMenu;

    /**
     * @var array<int, array<string, mixed>>
     */
    protected array $schemaRows = [];

    protected string $error = '';

    public function __construct(protected SchemaAnalyzerInterface $analyzer)
    {
        $tables = [];
        try {
            foreach ($this->analyzer->getTables() as $table) {
                $name = $this->normalizeTableName($table);
                $tables[$name] = $name;
            }
        } catch (Throwable $exception) {
            $this->error = 'Unable to load tables: '.$exception->getMessage();
        }

        if ($tables === []) {
            $tables = ['no_tables' => 'No tables found'];
        }

        $this->tablesMenu = new ChoiceFieldComponent($tables);
        $this->loadSchema();
    }

    public function name(): string
    {
        return 'Schema';
    }

    public function build(): Widget
    {
        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(1),
                Constraint::min(1),
                Constraint::length(1),
            )
            ->widgets(
                new BannerWidget('Database Schema'),
                GridWidget::default()
                    ->direction(Direction::Horizontal)
                    ->constraints(
                        Constraint::percentage(30),
                        Constraint::percentage(70),
                    )
                    ->widgets(
                        BlockWidget::default()
                            ->borders(Borders::ALL)
                            ->titles(Title::fromString('Tables'))
                            ->widget(
                                ParagraphWidget::fromText(Text::parse(implode("\n", $this->tablesMenu->lines())))
                            ),
                        BlockWidget::default()
                            ->borders(Borders::ALL)
                            ->titles(Title::fromString('Columns'))
                            ->widget(new SchemaTableWidget($this->schemaRows)),
                    ),
                new KeyHintsWidget([
                    'Up/Down' => 'Move',
                    'Esc' => 'Back',
                ])
            );
    }

    public function handle(object $event): Action
    {
        if ($this->isEscape($event)) {
            return new Navigate(HomeScreen::class);
        }

        $this->tablesMenu->handle($event);
        $this->loadSchema();

        return new Stay;
    }

    protected function loadSchema(): void
    {
        if ($this->error !== '') {
            $this->schemaRows = [[
                'name' => 'error',
                'type' => 'n/a',
                'nullable' => 'n/a',
                'default' => $this->error,
                'key' => '-',
            ]];

            return;
        }

        $selected = $this->tablesMenu->selectedKey();
        if ($selected === 'no_tables' || $selected === '') {
            $this->schemaRows = [];

            return;
        }

        $schema = $this->analyzer->getTableSchema($selected);
        $this->schemaRows = array_values($schema['columns'] ?? []);
    }

    protected function normalizeTableName(mixed $table): string
    {
        if (is_array($table)) {
            return (string) reset($table);
        }

        if (is_object($table)) {
            $vars = get_object_vars($table);

            return (string) reset($vars);
        }

        return (string) $table;
    }

    protected function isEscape(object $event): bool
    {
        return strtolower((string) ($event->code ?? '')) === 'esc';
    }
}
