<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Actions;

use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Screens\HomeScreen;
use Jemgdevp\Domo\Tests\TestCase;

class NavigateTest extends TestCase
{
    public function test_it_stores_destination_screen(): void
    {
        $action = new Navigate(HomeScreen::class);

        $this->assertSame(HomeScreen::class, $action->screen);
    }
}

