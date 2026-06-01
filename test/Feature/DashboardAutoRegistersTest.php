<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Tests\TestCase;

/**
 * The dashboard must be reachable by route alone (no domo:serve command)
 * whenever the current environment is in the allowed list.
 */
class DashboardAutoRegistersTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Allow the active test environment so the provider auto-registers
        // the routes without any manual route definition.
        $app['config']->set('domo.dashboard.environments', [$app->environment()]);
    }

    public function test_dashboard_routes_auto_register_without_a_command(): void
    {
        $this->assertTrue(Route::has('domo.index'));
        $this->assertTrue(Route::has('domo.schema'));
        $this->assertTrue(Route::has('domo.models'));
        $this->assertTrue(Route::has('domo.analyze'));
        $this->assertTrue(Route::has('domo.analyze.post'));
    }
}
