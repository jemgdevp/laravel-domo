<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Screens;

use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Stay;
use Jemgdevp\Domo\Services\TUI\Contracts\Action;
use Jemgdevp\Domo\Services\TUI\Contracts\Screen;
use Jemgdevp\Domo\Services\TUI\Widgets\BannerWidget;
use Jemgdevp\Domo\Services\TUI\Widgets\KeyHintsWidget;
use PhpTui\Tui\Extension\Core\Widget\GridWidget;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Constraint;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Direction;
use PhpTui\Tui\Widget\Widget;
use Throwable;

class AnalyzeScreen implements Screen
{
    public function __construct(
        protected AiDriverInterface $aiDriver,
        protected SchemaAnalyzerInterface $analyzer
    ) {}

    public function name(): string
    {
        return 'Analyze';
    }

    public function build(): Widget
    {
        $lines = ['Press Enter to run AI analysis for all tables.'];
        try {
            $schema = ['tables' => $this->analyzer->getTables()];
            $result = $this->aiDriver->analyzeSchema($schema);
            $lines[] = '';
            $lines[] = 'Last analysis result:';
            $lines[] = json_encode($result, JSON_PRETTY_PRINT) ?: 'No output';
        } catch (Throwable) {
            $lines[] = '';
            $lines[] = 'AI driver not configured yet.';
        }

        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(1),
                Constraint::min(1),
                Constraint::length(1),
            )
            ->widgets(
                new BannerWidget('AI Analysis'),
                ParagraphWidget::fromText(Text::parse(implode("\n", $lines))),
                new KeyHintsWidget(['Enter' => 'Run', 'Esc' => 'Back'])
            );
    }

    public function handle(object $event): Action
    {
        if (strtolower((string) ($event->code ?? '')) === 'esc') {
            return new Navigate(HomeScreen::class);
        }

        return new Stay;
    }
}
