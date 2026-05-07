<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Widget\Widget;

final readonly class SchemaTableWidget implements Widget
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function __construct(public array $rows) {}
}

