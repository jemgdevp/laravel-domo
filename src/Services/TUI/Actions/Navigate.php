<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Actions;

use Jemgdevp\Domo\Services\TUI\Contracts\Action;

final readonly class Navigate implements Action
{
    public function __construct(public string $screen) {}
}
