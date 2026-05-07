<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Widget\Widget;

final readonly class KeyHintsWidget implements Widget
{
    /**
     * @param  array<string, string>  $hints
     */
    public function __construct(public array $hints) {}
}

