<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\MCP;

use Jemgdevp\Domo\Contracts\McpServerInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Throwable;

class DomoMcpServer implements McpServerInterface
{
    /**
     * MCP protocol version implemented by this server.
     */
    protected const PROTOCOL_VERSION = '2024-11-05';

    /**
     * JSON-RPC parse error code.
     */
    protected const ERROR_PARSE = -32700;

    /**
     * JSON-RPC invalid request code.
     */
    protected const ERROR_INVALID_REQUEST = -32600;

    /**
     * JSON-RPC method not found code.
     */
    protected const ERROR_METHOD_NOT_FOUND = -32601;

    /**
     * JSON-RPC invalid params code.
     */
    protected const ERROR_INVALID_PARAMS = -32602;

    /**
     * JSON-RPC internal error code.
     */
    protected const ERROR_INTERNAL = -32603;

    /**
     * Whether the STDIO read loop is active.
     */
    protected bool $running = false;

    /**
     * Input stream resource (defaults to STDIN).
     *
     * @var resource|null
     */
    protected $input = null;

    /**
     * Output stream resource (defaults to STDOUT).
     *
     * @var resource|null
     */
    protected $output = null;

    public function __construct(
        protected SchemaAnalyzerInterface $analyzer
    ) {}

    /**
     * {@inheritDoc}
     *
     * Runs the JSON-RPC 2.0 server over the STDIO transport. Reads one JSON
     * message per line from STDIN, dispatches it and writes the response to
     * STDOUT. Notifications (messages without an id) produce no response.
     */
    public function start(): void
    {
        $input = $this->input ?? STDIN;
        $output = $this->output ?? STDOUT;

        $this->running = true;

        while ($this->running) {
            $line = fgets($input);

            if ($line === false) {
                // EOF: the client closed the pipe. Stop cleanly.
                break;
            }

            $line = trim($line);

            if ($line === '') {
                continue;
            }

            $response = $this->handleMessage($line);

            if ($response !== null) {
                $this->writeMessage($output, $response);
            }
        }

        $this->running = false;
    }

    /**
     * {@inheritDoc}
     *
     * Signals the STDIO read loop to terminate on its next iteration.
     */
    public function stop(): void
    {
        $this->running = false;
    }

    /**
     * Set the input stream used by the STDIO transport.
     *
     * Primarily intended for testing. Defaults to STDIN when unset.
     *
     * @param  resource  $stream
     */
    public function setInputStream($stream): void
    {
        $this->input = $stream;
    }

    /**
     * Set the output stream used by the STDIO transport.
     *
     * Primarily intended for testing. Defaults to STDOUT when unset.
     *
     * @param  resource  $stream
     */
    public function setOutputStream($stream): void
    {
        $this->output = $stream;
    }

    /**
     * {@inheritDoc}
     */
    public function handleRequest(array $request): array
    {
        $method = $request['method'] ?? '';

        return match ($method) {
            'schema/list' => $this->listTables(),
            'schema/describe' => $this->describeTable($request['params']['table'] ?? ''),
            'models/list' => $this->listModels(),
            'models/analyze' => $this->analyzeModel($request['params']['model'] ?? ''),
            default => ['error' => 'Unknown method'],
        };
    }

    /**
     * Parse and dispatch a single raw JSON-RPC message.
     *
     * @return array<string, mixed>|null The JSON-RPC response, or null for notifications.
     */
    public function handleMessage(string $raw): ?array
    {
        try {
            /** @var mixed $message */
            $message = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            return $this->errorResponse(null, self::ERROR_PARSE, 'Parse error');
        }

        if (! is_array($message)) {
            return $this->errorResponse(null, self::ERROR_INVALID_REQUEST, 'Invalid Request');
        }

        $id = $message['id'] ?? null;
        $method = $message['method'] ?? null;
        $isNotification = ! array_key_exists('id', $message);

        if (! is_string($method) || $method === '') {
            return $isNotification
                ? null
                : $this->errorResponse($id, self::ERROR_INVALID_REQUEST, 'Invalid Request');
        }

        /** @var array<string, mixed> $params */
        $params = is_array($message['params'] ?? null) ? $message['params'] : [];

        try {
            $result = $this->dispatch($method, $params);
        } catch (MethodNotFoundException $exception) {
            return $isNotification
                ? null
                : $this->errorResponse($id, self::ERROR_METHOD_NOT_FOUND, $exception->getMessage());
        } catch (InvalidParamsException $exception) {
            return $isNotification
                ? null
                : $this->errorResponse($id, self::ERROR_INVALID_PARAMS, $exception->getMessage());
        } catch (Throwable $exception) {
            return $isNotification
                ? null
                : $this->errorResponse($id, self::ERROR_INTERNAL, $exception->getMessage());
        }

        // Notifications never receive a response, even on success.
        if ($isNotification) {
            return null;
        }

        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'result' => $result,
        ];
    }

    /**
     * Dispatch an MCP protocol method to its handler.
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws MethodNotFoundException
     * @throws InvalidParamsException
     */
    protected function dispatch(string $method, array $params): array
    {
        return match ($method) {
            'initialize' => $this->handleInitialize(),
            'tools/list' => $this->handleToolsList(),
            'tools/call' => $this->handleToolsCall($params),
            'ping' => [],
            default => throw new MethodNotFoundException("Method not found: {$method}"),
        };
    }

    /**
     * Build the MCP `initialize` handshake response.
     *
     * @return array<string, mixed>
     */
    protected function handleInitialize(): array
    {
        return [
            'protocolVersion' => self::PROTOCOL_VERSION,
            'capabilities' => [
                'tools' => ['listChanged' => false],
            ],
            'serverInfo' => [
                'name' => 'laravel-domo',
                'version' => '1.0.0',
            ],
        ];
    }

    /**
     * Build the MCP `tools/list` response exposing the schema operations.
     *
     * @return array<string, mixed>
     */
    protected function handleToolsList(): array
    {
        $tools = array_map(
            static function (array $definition): array {
                unset($definition['_method']);

                return $definition;
            },
            array_values($this->toolDefinitions())
        );

        return [
            'tools' => $tools,
        ];
    }

    /**
     * Handle an MCP `tools/call` request by mapping it onto handleRequest().
     *
     * @param  array<string, mixed>  $params
     * @return array<string, mixed>
     *
     * @throws MethodNotFoundException
     */
    protected function handleToolsCall(array $params): array
    {
        $name = $params['name'] ?? null;

        if (! is_string($name) || $name === '') {
            throw new InvalidParamsException('Tool name is required');
        }

        $definitions = $this->toolDefinitions();

        if (! isset($definitions[$name])) {
            throw new MethodNotFoundException("Unknown tool: {$name}");
        }

        /** @var array<string, mixed> $arguments */
        $arguments = is_array($params['arguments'] ?? null) ? $params['arguments'] : [];

        $result = $this->handleRequest([
            'method' => $definitions[$name]['_method'],
            'params' => $arguments,
        ]);

        if (isset($result['error'])) {
            return [
                'content' => [
                    ['type' => 'text', 'text' => (string) $result['error']],
                ],
                'isError' => true,
            ];
        }

        return [
            'content' => [
                [
                    'type' => 'text',
                    'text' => (string) json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ],
            ],
            'isError' => false,
        ];
    }

    /**
     * MCP tool definitions keyed by tool name.
     *
     * Each definition carries a private `_method` key mapping the tool back to
     * the internal handleRequest() method; it is stripped from the public
     * tools/list output.
     *
     * @return array<string, array<string, mixed>>
     */
    protected function toolDefinitions(): array
    {
        return [
            'schema_list' => [
                'name' => 'schema_list',
                'description' => 'List all database tables in the application schema.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [],
                    'additionalProperties' => false,
                ],
                '_method' => 'schema/list',
            ],
            'schema_describe' => [
                'name' => 'schema_describe',
                'description' => 'Describe the columns and structure of a specific database table.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'table' => [
                            'type' => 'string',
                            'description' => 'The name of the table to describe.',
                        ],
                    ],
                    'required' => ['table'],
                    'additionalProperties' => false,
                ],
                '_method' => 'schema/describe',
            ],
            'models_list' => [
                'name' => 'models_list',
                'description' => 'List all discovered Eloquent models in the application.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [],
                    'additionalProperties' => false,
                ],
                '_method' => 'models/list',
            ],
            'models_analyze' => [
                'name' => 'models_analyze',
                'description' => 'Analyze the relationships of a specific Eloquent model.',
                'inputSchema' => [
                    'type' => 'object',
                    'properties' => [
                        'model' => [
                            'type' => 'string',
                            'description' => 'The fully-qualified class name of the model to analyze.',
                        ],
                    ],
                    'required' => ['model'],
                    'additionalProperties' => false,
                ],
                '_method' => 'models/analyze',
            ],
        ];
    }

    /**
     * Build a JSON-RPC 2.0 error response.
     *
     * @param  string|int|null  $id
     * @return array<string, mixed>
     */
    protected function errorResponse($id, int $code, string $message): array
    {
        return [
            'jsonrpc' => '2.0',
            'id' => $id,
            'error' => [
                'code' => $code,
                'message' => $message,
            ],
        ];
    }

    /**
     * Encode and write a JSON-RPC message to the output stream (newline framed).
     *
     * @param  resource  $output
     * @param  array<string, mixed>  $message
     */
    protected function writeMessage($output, array $message): void
    {
        $encoded = json_encode($message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($encoded === false) {
            $encoded = (string) json_encode(
                $this->errorResponse($message['id'] ?? null, self::ERROR_INTERNAL, 'Failed to encode response')
            );
        }

        fwrite($output, $encoded."\n");
        fflush($output);
    }

    /**
     * List all database tables.
     *
     * @return array<string, mixed>
     */
    protected function listTables(): array
    {
        return [
            'tables' => $this->analyzer->getTables(),
        ];
    }

    /**
     * Describe a specific table.
     *
     * @return array<string, mixed>
     */
    protected function describeTable(string $table): array
    {
        if (empty($table)) {
            return ['error' => 'Table name required'];
        }

        return [
            'schema' => $this->analyzer->getTableSchema($table),
        ];
    }

    /**
     * List all Eloquent models.
     *
     * @return array<string, mixed>
     */
    protected function listModels(): array
    {
        return [
            'models' => $this->analyzer->getModels(),
        ];
    }

    /**
     * Analyze a specific model.
     *
     * @return array<string, mixed>
     */
    protected function analyzeModel(string $model): array
    {
        if (empty($model)) {
            return ['error' => 'Model name required'];
        }

        return [
            'relationships' => $this->analyzer->analyzeModelRelationships($model),
        ];
    }
}
