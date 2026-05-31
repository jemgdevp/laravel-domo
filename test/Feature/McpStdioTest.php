<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\MCP\DomoMcpServer;
use Jemgdevp\Domo\Tests\TestCase;

class McpStdioTest extends TestCase
{
    /**
     * Build a server backed by a mocked schema analyzer.
     *
     * @param  array<string, mixed>  $expectations
     */
    private function makeServer(array $expectations = []): DomoMcpServer
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);

        $analyzer->method('getTables')->willReturn(
            $expectations['tables'] ?? ['users' => [], 'posts' => []]
        );
        $analyzer->method('getTableSchema')->willReturn(
            $expectations['schema'] ?? ['columns' => ['id', 'name']]
        );
        $analyzer->method('getModels')->willReturn(
            $expectations['models'] ?? ['App\\Models\\User']
        );
        $analyzer->method('analyzeModelRelationships')->willReturn(
            $expectations['relationships'] ?? ['hasMany' => ['posts']]
        );

        return new DomoMcpServer($analyzer);
    }

    public function test_handle_request_schema_list_returns_tables(): void
    {
        $server = $this->makeServer(['tables' => ['users' => [], 'posts' => []]]);

        $result = $server->handleRequest(['method' => 'schema/list']);

        $this->assertSame(['tables' => ['users' => [], 'posts' => []]], $result);
    }

    public function test_handle_request_unknown_method_returns_unknown_error(): void
    {
        $server = $this->makeServer();

        $result = $server->handleRequest(['method' => 'does/not/exist']);

        $this->assertSame(['error' => 'Unknown method'], $result);
    }

    public function test_handle_message_initialize_returns_server_info(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
            'params' => [],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertIsArray($response);
        $this->assertSame('2.0', $response['jsonrpc']);
        $this->assertSame(1, $response['id']);
        $this->assertArrayHasKey('result', $response);
        $this->assertSame('2024-11-05', $response['result']['protocolVersion']);
        $this->assertSame(
            ['name' => 'laravel-domo', 'version' => '1.0.0'],
            $response['result']['serverInfo']
        );
        $this->assertArrayHasKey('tools', $response['result']['capabilities']);
    }

    public function test_handle_message_tools_list_returns_expected_tools(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
        ]);

        $response = $server->handleMessage($raw);

        $this->assertIsArray($response);
        $this->assertArrayHasKey('tools', $response['result']);

        $names = array_map(
            static fn (array $tool): string => $tool['name'],
            $response['result']['tools']
        );

        $this->assertSame(
            ['schema_list', 'schema_describe', 'models_list', 'models_analyze'],
            $names
        );
    }

    public function test_handle_message_tools_list_does_not_leak_internal_method_key(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 3,
            'method' => 'tools/list',
        ]);

        $response = $server->handleMessage($raw);

        foreach ($response['result']['tools'] as $tool) {
            $this->assertArrayNotHasKey('_method', $tool);
            $this->assertArrayHasKey('inputSchema', $tool);
            $this->assertArrayHasKey('description', $tool);
        }
    }

    public function test_handle_message_tools_call_dispatches_to_schema_list(): void
    {
        $server = $this->makeServer(['tables' => ['users' => []]]);

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 4,
            'method' => 'tools/call',
            'params' => [
                'name' => 'schema_list',
                'arguments' => [],
            ],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertIsArray($response);
        $this->assertFalse($response['result']['isError']);
        $this->assertSame('text', $response['result']['content'][0]['type']);

        $decoded = json_decode($response['result']['content'][0]['text'], true);
        $this->assertSame(['tables' => ['users' => []]], $decoded);
    }

    public function test_handle_message_tools_call_forwards_arguments(): void
    {
        $server = $this->makeServer(['schema' => ['columns' => ['id', 'email']]]);

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 5,
            'method' => 'tools/call',
            'params' => [
                'name' => 'schema_describe',
                'arguments' => ['table' => 'users'],
            ],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertFalse($response['result']['isError']);

        $decoded = json_decode($response['result']['content'][0]['text'], true);
        $this->assertSame(['schema' => ['columns' => ['id', 'email']]], $decoded);
    }

    public function test_handle_message_tools_call_with_handler_error_flags_is_error(): void
    {
        $server = $this->makeServer();

        // schema_describe with an empty table makes handleRequest return an error.
        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 6,
            'method' => 'tools/call',
            'params' => [
                'name' => 'schema_describe',
                'arguments' => ['table' => ''],
            ],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertTrue($response['result']['isError']);
        $this->assertSame('Table name required', $response['result']['content'][0]['text']);
    }

    public function test_handle_message_tools_call_unknown_tool_returns_method_not_found(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 7,
            'method' => 'tools/call',
            'params' => ['name' => 'nope'],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertArrayHasKey('error', $response);
        $this->assertSame(-32601, $response['error']['code']);
        $this->assertSame('Unknown tool: nope', $response['error']['message']);
    }

    public function test_handle_message_tools_call_missing_name_returns_invalid_params(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 8,
            'method' => 'tools/call',
            'params' => [],
        ]);

        $response = $server->handleMessage($raw);

        $this->assertSame(-32602, $response['error']['code']);
        $this->assertSame('Tool name is required', $response['error']['message']);
    }

    public function test_handle_message_unknown_method_returns_method_not_found_error(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 9,
            'method' => 'totally/unknown',
        ]);

        $response = $server->handleMessage($raw);

        $this->assertArrayHasKey('error', $response);
        $this->assertSame('2.0', $response['jsonrpc']);
        $this->assertSame(9, $response['id']);
        $this->assertSame(-32601, $response['error']['code']);
        $this->assertSame('Method not found: totally/unknown', $response['error']['message']);
    }

    public function test_handle_message_invalid_json_returns_parse_error(): void
    {
        $server = $this->makeServer();

        $response = $server->handleMessage('{not valid json');

        $this->assertNull($response['id']);
        $this->assertSame(-32700, $response['error']['code']);
        $this->assertSame('Parse error', $response['error']['message']);
    }

    public function test_handle_message_notification_without_id_returns_null(): void
    {
        $server = $this->makeServer();

        // No "id" key => notification; even on success there is no response.
        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'method' => 'ping',
        ]);

        $this->assertNull($server->handleMessage($raw));
    }

    public function test_handle_message_notification_for_unknown_method_returns_null(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'method' => 'unknown/notification',
        ]);

        $this->assertNull($server->handleMessage($raw));
    }

    public function test_handle_message_ping_request_returns_empty_result(): void
    {
        $server = $this->makeServer();

        $raw = (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 10,
            'method' => 'ping',
        ]);

        $response = $server->handleMessage($raw);

        $this->assertSame([], $response['result']);
        $this->assertSame(10, $response['id']);
    }

    public function test_start_processes_buffered_messages_from_input_stream(): void
    {
        $server = $this->makeServer(['tables' => ['users' => []]]);

        // Drive the read loop with an in-memory stream instead of real STDIN.
        $input = fopen('php://memory', 'r+');
        $output = fopen('php://memory', 'r+');
        $this->assertIsResource($input);
        $this->assertIsResource($output);

        fwrite($input, (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'initialize',
        ])."\n");
        fwrite($input, "\n"); // blank line is skipped
        fwrite($input, (string) json_encode([
            'jsonrpc' => '2.0',
            'id' => 2,
            'method' => 'tools/list',
        ])."\n");
        rewind($input);

        $server->setInputStream($input);
        $server->setOutputStream($output);
        $server->start();

        rewind($output);
        $written = (string) stream_get_contents($output);
        $lines = array_values(array_filter(explode("\n", $written), static fn (string $l): bool => $l !== ''));

        $this->assertCount(2, $lines);

        $first = json_decode($lines[0], true);
        $this->assertSame(1, $first['id']);
        $this->assertSame('laravel-domo', $first['result']['serverInfo']['name']);

        $second = json_decode($lines[1], true);
        $this->assertSame(2, $second['id']);
        $this->assertArrayHasKey('tools', $second['result']);

        fclose($input);
        fclose($output);
    }
}
