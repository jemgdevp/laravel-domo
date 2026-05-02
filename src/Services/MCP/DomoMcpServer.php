<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\MCP;

use Jemgdevp\Domo\Contracts\McpServerInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;

class DomoMcpServer implements McpServerInterface
{
    /**
     * @param SchemaAnalyzerInterface $analyzer
     */
    public function __construct(
        protected SchemaAnalyzerInterface $analyzer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function start(): void
    {
        // TODO: Implement MCP server start logic
    }

    /**
     * @inheritDoc
     */
    public function stop(): void
    {
        // TODO: Implement MCP server stop logic
    }

    /**
     * @inheritDoc
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
     * @param string $table
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
     * @param string $model
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
