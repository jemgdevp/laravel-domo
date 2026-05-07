<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Screens;

use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Quit;
use Jemgdevp\Domo\Services\TUI\Screens\HomeScreen;
use Jemgdevp\Domo\Tests\TestCase;

class HomeScreenTest extends TestCase
{
    public function test_escape_returns_quit_action(): void
    {
        $screen = new HomeScreen();

        $action = $screen->handle((object) ['code' => 'esc']);

        $this->assertInstanceOf(Quit::class, $action);
    }

    public function test_enter_returns_navigation_action(): void
    {
        $screen = new HomeScreen();

        $action = $screen->handle((object) ['code' => 'enter']);

        $this->assertInstanceOf(Navigate::class, $action);
    }
}

