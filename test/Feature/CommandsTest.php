<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Jemgdevp\Domo\Services\TUI\ScreenManager;
use Jemgdevp\Domo\Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test_domo_serve_command_exists(): void
    {
        $this->artisan('domo:serve')
            ->expectsOutputToContain('Starting Laravel Domo Dashboard')
            ->assertSuccessful();
    }

    public function test_domo_tui_command_exists(): void
    {
        $this->mock(ScreenManager::class, function ($mock) {
            $mock->shouldReceive('run')->once();
        });

        $this->artisan('domo:tui')
            ->expectsOutputToContain('Laravel Domo TUI')
            ->assertSuccessful();
    }
}
