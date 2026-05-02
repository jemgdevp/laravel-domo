<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Contracts;

interface McpServerInterface
{
    /**
     * Start MCP server.
     */
    public function start(): void;

    /**
     * Stop MCP server.
     */
    public function stop(): void;

    /**
     * Handle MCP request.
     *
     * @param  array<string, mixed>  $request
     * @return array<string, mixed>
     */
    public function handleRequest(array $request): array;
}
