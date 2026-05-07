<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Widgets;

use PhpTui\Tui\Widget\Widget;

final readonly class BannerWidget implements Widget
{
    public function __construct(public string $title = 'Laravel Domo TUI') {}
}

