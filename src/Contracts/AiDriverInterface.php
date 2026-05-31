<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Contracts;

interface AiDriverInterface
{
    /**
     * Analyze database schema and return insights.
     *
     * @param  array<string, mixed>  $schema
     * @return array<string, mixed>
     */
    public function analyzeSchema(array $schema): array;

    /**
     * Generate migration from schema analysis.
     *
     * @param  array<string, mixed>  $analysis
     */
    public function generateMigration(array $analysis): string;

    /**
     * Suggest Eloquent relationships.
     *
     * @param  array<array-key, mixed>  $models
     * @return array<string, mixed>
     */
    public function suggestRelationships(array $models): array;
}
