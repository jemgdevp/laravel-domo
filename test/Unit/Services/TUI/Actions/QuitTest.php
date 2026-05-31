<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Actions;

use Jemgdevp\Domo\Services\TUI\Actions\Quit;
use Jemgdevp\Domo\Tests\TestCase;

class QuitTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $action = new Quit;

        $this->assertInstanceOf(Quit::class, $action);
    }
}
