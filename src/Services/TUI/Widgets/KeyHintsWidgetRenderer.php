<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

class KeyHintsWidgetRenderer implements WidgetRenderer
{
    public function render(WidgetRenderer $renderer, Widget $widget, Buffer $buffer, Area $area): void
    {
        if (! $widget instanceof KeyHintsWidget) {
            return;
        }

        $parts = [];
        foreach ($widget->hints as $key => $description) {
            $parts[] = sprintf('[%s] %s', $key, $description);
        }

        $renderer->render(
            $renderer,
            ParagraphWidget::fromText(Text::parse(implode(' | ', $parts))),
            $buffer,
            $area
        );
    }
}
