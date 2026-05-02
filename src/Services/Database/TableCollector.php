<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Database;

/**
 * Table metadata collector.
 *
 * Gathers information about database tables including
 * columns, indexes, foreign keys, and constraints.
 */
class TableCollector
{
    /**
     * Create a new table collector instance.
     */
    public function __construct(
        protected ConnectionManager $connection
    ) {}

    /**
     * Get all table names.
     *
     * @return array<string>
     */
    public function getTableNames(): array
    {
        $tables = $this->connection->getConnection()->select(
            'SHOW TABLES'
        );

        return collect($tables)
            ->flatten()
            ->map(fn ($table) => is_array($table) ? reset($table) : $table)
            ->toArray();
    }

    /**
     * Get table columns.
     *
     * @return array<string, mixed>
     */
    public function getColumns(string $table): array
    {
        $grammar = $this->connection->getConnection()->getSchemaGrammar();
        $wrapped = $grammar->wrapTable($table);

        return $this->connection->getConnection()->select(
            "DESCRIBE {$wrapped}"
        );
    }

    /**
     * Get table indexes.
     *
     * @return array<string, mixed>
     */
    public function getIndexes(string $table): array
    {
        $grammar = $this->connection->getConnection()->getSchemaGrammar();
        $wrapped = $grammar->wrapTable($table);

        return $this->connection->getConnection()->select(
            "SHOW INDEX FROM {$wrapped}"
        );
    }

    /**
     * Get table foreign keys.
     *
     * @return array<string, mixed>
     */
    public function getForeignKeys(string $table): array
    {
        $database = $this->connection->getDatabaseName();

        return $this->connection->getConnection()->select(
            'SELECT 
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$database, $table]
        );
    }
}
