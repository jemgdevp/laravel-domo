<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Contracts;

use PhpTui\Tui\Widget\Widget;

interface Screen
{
    public function name(): string;

    public function build(): Widget;

    public function handle(object $event): Action;
}

