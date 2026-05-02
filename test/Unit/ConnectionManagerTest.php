<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Services\Database\ConnectionManager;
use Jemgdevp\Domo\Tests\TestCase;

class ConnectionManagerTest extends TestCase
{
    public function test_connection_manager_can_be_instantiated(): void
    {
        $manager = new ConnectionManager;
        $this->assertInstanceOf(ConnectionManager::class, $manager);
    }

    public function test_connection_manager_accepts_connection_name(): void
    {
        $manager = new ConnectionManager('mysql');
        $this->assertInstanceOf(ConnectionManager::class, $manager);
    }
}
