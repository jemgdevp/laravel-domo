<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Screens;

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

class ExportScreen implements Screen
{
    public function name(): string
    {
        return 'Export';
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
                new BannerWidget('Export SQL'),
                ParagraphWidget::fromText(Text::parse('Coming soon: SQL export and dump helpers.')),
                new KeyHintsWidget(['Esc' => 'Back'])
            );
    }

    public function handle(object $event): Action
    {
        if (strtolower((string) ($event->code ?? '')) === 'esc') {
            return new Navigate(HomeScreen::class);
        }

        return new Stay();
    }
}

