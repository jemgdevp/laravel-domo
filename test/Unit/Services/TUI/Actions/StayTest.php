<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Actions;

use Jemgdevp\Domo\Services\TUI\Actions\Stay;
use Jemgdevp\Domo\Tests\TestCase;

class StayTest extends TestCase
{
    public function test_it_can_be_instantiated(): void
    {
        $action = new Stay();

        $this->assertInstanceOf(Stay::class, $action);
    }
}

