<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Jemgdevp\Domo\Tests\TestCase;

class CommandsTest extends TestCase
{
    public function test_domo_serve_command_exists(): void
    {
        $this->artisan('domo:serve')
            ->expectsOutputToContain('Starting Domo Dashboard server')
            ->assertSuccessful();
    }

    public function test_domo_tui_command_exists(): void
    {
        $this->artisan('domo:tui')
            ->expectsOutputToContain('Launching Domo TUI')
            ->assertSuccessful();
    }
}
