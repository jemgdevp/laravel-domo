<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Commands;

use Illuminate\Console\Command;
use Jemgdevp\Domo\Contracts\McpServerInterface;
use Throwable;

/**
 * Start the Laravel Domo MCP server over the STDIO transport.
 *
 * Speaks JSON-RPC 2.0 over STDIN/STDOUT as consumed by MCP clients such as
 * Claude Desktop and Claude Code. No decorative output is written to STDOUT,
 * since that would corrupt the protocol stream; diagnostics go to STDERR.
 *
 * @example php artisan domo:mcp
 */
class DomoMcpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domo:mcp';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the Laravel Domo MCP server over stdio (JSON-RPC 2.0)';

    /**
     * Execute the console command.
     */
    public function handle(McpServerInterface $server): int
    {
        try {
            $server->start();
        } catch (Throwable $exception) {
            $this->writeToStandardError('domo:mcp fatal error: '.$exception->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    /**
     * Write a diagnostic line to STDERR.
     *
     * STDOUT is reserved for the JSON-RPC transport, so all human-readable
     * output must go to STDERR to avoid corrupting the protocol stream.
     */
    protected function writeToStandardError(string $message): void
    {
        $stderr = fopen('php://stderr', 'w');

        if ($stderr !== false) {
            fwrite($stderr, $message."\n");
            fclose($stderr);
        }
    }
}
