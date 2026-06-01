<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Tests\TestCase;

/**
 * The `enabled` flag is an independent kill switch: even in an allowed
 * environment, disabling the dashboard must prevent route registration.
 */
class DashboardDisabledTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // Environment IS allowed, but the dashboard is turned off.
        $app['config']->set('domo.dashboard.environments', [$app->environment()]);
        $app['config']->set('domo.dashboard.enabled', false);
    }

    public function test_disabling_the_dashboard_prevents_route_registration(): void
    {
        $this->assertFalse(Route::has('domo.index'));
        $this->get('/domo')->assertNotFound();
    }
}
