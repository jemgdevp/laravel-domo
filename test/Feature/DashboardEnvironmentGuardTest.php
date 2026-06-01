<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Illuminate\Support\Facades\Route;
use Jemgdevp\Domo\Tests\TestCase;

/**
 * The dashboard is a local tool: outside the allowed environments (e.g.
 * production) its routes must not register at all — defense in depth.
 */
class DashboardEnvironmentGuardTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        // The active test environment is NOT in this list, so the gate must
        // refuse registration — mirroring how production behaves.
        $app['config']->set('domo.dashboard.environments', ['production']);
    }

    public function test_dashboard_routes_do_not_register_outside_allowed_environments(): void
    {
        $this->assertFalse(Route::has('domo.index'));
        $this->assertFalse(Route::has('domo.schema'));
        $this->assertFalse(Route::has('domo.analyze.post'));

        $this->get('/domo')->assertNotFound();
    }
}
