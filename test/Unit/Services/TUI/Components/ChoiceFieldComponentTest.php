<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Components;

use Jemgdevp\Domo\Services\TUI\Components\ChoiceFieldComponent;
use Jemgdevp\Domo\Services\TUI\Components\ComponentState;
use Jemgdevp\Domo\Tests\TestCase;

class ChoiceFieldComponentTest extends TestCase
{
    public function test_it_moves_selection_and_submits(): void
    {
        $component = new ChoiceFieldComponent([
            'first' => 'First',
            'second' => 'Second',
        ]);

        $downEvent = (object) ['code' => 'down'];
        $enterEvent = (object) ['code' => 'enter'];

        $moveState = $component->handle($downEvent);
        $submitState = $component->handle($enterEvent);

        $this->assertSame(ComponentState::Handled, $moveState);
        $this->assertSame(ComponentState::Submitted, $submitState);
        $this->assertSame('second', $component->selectedKey());
    }
}

