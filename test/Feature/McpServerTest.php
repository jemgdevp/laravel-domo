<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Jemgdevp\Domo\Tests\TestCase;

class McpServerTest extends TestCase
{
    public function test_mcp_server_can_be_enabled(): void
    {
        config(['domo.mcp.enabled' => true]);

        $this->assertTrue(config('domo.mcp.enabled'));
    }

    public function test_mcp_server_port_configuration(): void
    {
        config(['domo.mcp.port' => 3001]);

        $this->assertEquals(3001, config('domo.mcp.port'));
    }
}
