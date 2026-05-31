<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\MCP;

use Jemgdevp\Domo\Exceptions\McpServerException;

/**
 * Thrown internally when JSON-RPC request params are missing or malformed.
 *
 * Mapped to the JSON-RPC -32602 (invalid params) error response.
 */
class InvalidParamsException extends McpServerException {}
