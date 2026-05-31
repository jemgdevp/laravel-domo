<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Display\Area;
use PhpTui\Tui\Display\Buffer;
use PhpTui\Tui\Extension\Core\Widget\ParagraphWidget;
use PhpTui\Tui\Text\Text;
use PhpTui\Tui\Widget\Widget;
use PhpTui\Tui\Widget\WidgetRenderer;

class SchemaTableWidgetRenderer implements WidgetRenderer
{
    public function __construct(protected bool $simple = false) {}

    public function render(WidgetRenderer $renderer, Widget $widget, Buffer $buffer, Area $area): void
    {
        if (! $widget instanceof SchemaTableWidget) {
            return;
        }

        $separator = $this->simple ? '-' : '=';
        $lines = ['name | type | nullable | default | key', str_repeat($separator, 72)];
        foreach ($widget->rows as $row) {
            $lines[] = sprintf(
                '%s | %s | %s | %s | %s',
                (string) ($row['name'] ?? '-'),
                (string) ($row['type'] ?? '-'),
                (string) ($row['nullable'] ?? '-'),
                (string) ($row['default'] ?? '-'),
                (string) ($row['key'] ?? '-')
            );
        }

        $renderer->render(
            $renderer,
            ParagraphWidget::fromText(Text::parse(implode("\n", $lines))),
            $buffer,
            $area
        );
    }
}
