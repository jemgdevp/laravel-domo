<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Screens;

use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Quit;
use Jemgdevp\Domo\Services\TUI\Actions\Stay;
use Jemgdevp\Domo\Services\TUI\Components\ChoiceFieldComponent;
use Jemgdevp\Domo\Services\TUI\Components\ComponentState;
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

class HomeScreen implements Screen
{
    protected ChoiceFieldComponent $menu;

    public function __construct()
    {
        $this->menu = new ChoiceFieldComponent([
            SchemaScreen::class => 'View Database Schema',
            ModelsScreen::class => 'View Eloquent Models',
            AnalyzeScreen::class => 'AI Analysis',
            MigrationsScreen::class => 'Manage Migrations',
            ExportScreen::class => 'Export SQL',
            'quit' => 'Exit',
        ]);
    }

    public function name(): string
    {
        return 'Home';
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
                new BannerWidget('Laravel Domo - Main Menu'),
                ParagraphWidget::fromText(Text::parse(implode("\n", $this->menu->lines()))),
                new KeyHintsWidget([
                    'Up/Down' => 'Navigate',
                    'Enter' => 'Select',
                    'Esc' => 'Quit',
                ])
            );
    }

    public function handle(object $event): Action
    {
        if ($this->isEscape($event)) {
            return new Quit;
        }

        $result = $this->menu->handle($event);
        if ($result === ComponentState::Submitted) {
            $selected = $this->menu->selectedKey();
            if ($selected === 'quit') {
                return new Quit;
            }

            return new Navigate($selected);
        }

        return new Stay;
    }

    protected function isEscape(object $event): bool
    {
        return strtolower((string) ($event->code ?? '')) === 'esc';
    }
}
