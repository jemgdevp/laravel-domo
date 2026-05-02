<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Contracts;

interface SchemaAnalyzerInterface
{
    /**
     * Get all database tables.
     *
     * @return array<string, mixed>
     */
    public function getTables(): array;

    /**
     * Get table schema.
     *
     * @param string $table
     * @return array<string, mixed>
     */
    public function getTableSchema(string $table): array;

    /**
     * Get Eloquent models.
     *
     * @return array<string, mixed>
     */
    public function getModels(): array;

    /**
     * Analyze model relationships.
     *
     * @param string $model
     * @return array<string, mixed>
     */
    public function analyzeModelRelationships(string $model): array;
}
