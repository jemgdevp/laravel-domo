<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Contracts\McpServerInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\MCP\DomoMcpServer;
use Jemgdevp\Domo\Tests\TestCase;

class McpServerTest extends TestCase
{
    public function test_mcp_server_can_be_instantiated(): void
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);
        $server = new DomoMcpServer($analyzer);
        $this->assertInstanceOf(DomoMcpServer::class, $server);
    }

    public function test_mcp_server_implements_interface(): void
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);
        $server = new DomoMcpServer($analyzer);
        $this->assertInstanceOf(McpServerInterface::class, $server);
    }

    public function test_mcp_server_handles_unknown_method(): void
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);
        $server = new DomoMcpServer($analyzer);

        $result = $server->handleRequest(['method' => 'unknown']);

        $this->assertEquals(['error' => 'Unknown method'], $result);
    }
}
