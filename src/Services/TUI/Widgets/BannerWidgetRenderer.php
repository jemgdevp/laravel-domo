<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Buffer\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Layout\Area;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

class BannerWidgetRenderer implements WidgetRenderer
{
    public function __construct(protected bool $colors = true) {}

    public function render(WidgetRenderer $renderer, Widget $widget, Buffer $buffer, Area $area): void
    {
        if (! $widget instanceof BannerWidget) {
            return;
        }

        $text = $this->colors
            ? '<options=bold;fg=blue>'.$widget->title.'</>'
            : $widget->title;

        $renderer->render(
            $renderer,
            ParagraphWidget::fromText(Text::parse($text)),
            $buffer,
            $area
        );
    }
}

