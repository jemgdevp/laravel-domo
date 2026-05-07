<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Screens;

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

class ModelsScreen implements Screen
{
    public function __construct(protected SchemaAnalyzerInterface $analyzer) {}

    public function name(): string
    {
        return 'Models';
    }

    public function build(): Widget
    {
        $models = $this->analyzer->getModels();
        $lines = $models === [] ? ['No models found'] : array_map(
            static fn (string $model): string => '- '.$model,
            $models
        );

        return GridWidget::default()
            ->direction(Direction::Vertical)
            ->constraints(
                Constraint::length(1),
                Constraint::min(1),
                Constraint::length(1),
            )
            ->widgets(
                new BannerWidget('Eloquent Models'),
                ParagraphWidget::fromText(Text::parse(implode("\n", $lines))),
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

