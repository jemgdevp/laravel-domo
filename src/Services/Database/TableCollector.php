<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Database;

use Illuminate\Support\Collection;

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
     *
     * @param ConnectionManager $connection
     */
    public function __construct(
        protected ConnectionManager $connection
    ) {
    }

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
            ->map(fn($table) => is_array($table) ? reset($table) : $table)
            ->toArray();
    }

    /**
     * Get table columns.
     *
     * @param string $table
     * @return array<string, mixed>
     */
    public function getColumns(string $table): array
    {
        return $this->connection->getConnection()->select(
            "DESCRIBE {$table}"
        );
    }

    /**
     * Get table indexes.
     *
     * @param string $table
     * @return array<string, mixed>
     */
    public function getIndexes(string $table): array
    {
        return $this->connection->getConnection()->select(
            "SHOW INDEX FROM {$table}"
        );
    }

    /**
     * Get table foreign keys.
     *
     * @param string $table
     * @return array<string, mixed>
     */
    public function getForeignKeys(string $table): array
    {
        $database = $this->connection->getDatabaseName();

        return $this->connection->getConnection()->select(
            "SELECT 
                COLUMN_NAME,
                CONSTRAINT_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
             FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL",
            [$database, $table]
        );
    }
}
