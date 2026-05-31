<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\MCP;

use Jemgdevp\Domo\Exceptions\McpServerException;

/**
 * Thrown internally when a JSON-RPC method or tool is not recognised.
 *
 * Mapped to the JSON-RPC -32601 (method not found) error response.
 */
class MethodNotFoundException extends McpServerException {}
