<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\TUI\Components;

enum ComponentState: string
{
    case Handled = 'handled';
    case Submitted = 'submitted';
    case Ignored = 'ignored';
}
