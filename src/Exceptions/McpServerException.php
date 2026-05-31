<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Exceptions;

class McpServerException extends DomoException
{
    /**
     * Create an exception for an unknown tool name.
     */
    public static function unknownTool(string $tool): self
    {
        return new self("Unknown MCP tool: {$tool}");
    }

    /**
     * Create an exception for a malformed JSON-RPC message.
     */
    public static function invalidMessage(string $reason): self
    {
        return new self("Invalid JSON-RPC message: {$reason}");
    }

    /**
     * Create an exception for an STDIO transport failure.
     */
    public static function transportFailure(string $reason): self
    {
        return new self("MCP transport failure: {$reason}");
    }
}
