<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Schema;

use Illuminate\Database\ConnectionInterface;
use Illuminate\Support\Facades\DB;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Exceptions\SchemaAnalyzerException;

class Analyzer implements SchemaAnalyzerInterface
{
    /**
     * {@inheritDoc}
     */
    public function getTables(): array
    {
        $connection = DB::connection();

        return match ($connection->getDriverName()) {
            'mysql' => $connection->select('SHOW TABLES'),
            'pgsql' => $connection->select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'"),
            'sqlite' => $connection->select("SELECT name FROM sqlite_master WHERE type = 'table' AND name != 'sqlite_sequence'"),
            'sqlsrv' => $connection->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'"),
            default => throw new SchemaAnalyzerException('Unsupported database driver: '.$connection->getDriverName()),
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getTableSchema(string $table): array
    {
        $connection = DB::connection();
        $driver = $connection->getDriverName();
        // Use the query grammar (always initialized on the connection) rather
        // than the schema grammar, which stays null until the schema builder is
        // first resolved and would trip "wrapTable() on null" on a fresh request.
        $grammar = $connection->getQueryGrammar();
        $wrappedTable = $grammar->wrapTable($table);

        $columns = $this->getColumnsForDriver($connection, $driver, $wrappedTable, $table);
        $primaryKeys = $this->getPrimaryKeysForDriver($connection, $driver, $table, $columns);
        $normalizedColumns = $this->normalizeColumns($columns, $driver, $primaryKeys);
        $indexes = $this->getIndexesForDriver($connection, $driver, $wrappedTable, $table);

        return [
            'columns' => $normalizedColumns,
            'indexes' => $indexes,
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function getModels(): array
    {
        $models = [];
        $modelPath = app_path('Models');

        if (! is_dir($modelPath)) {
            return $models;
        }

        foreach (glob($modelPath.'/*.php') as $file) {
            $className = 'App\\Models\\'.basename($file, '.php');
            if (class_exists($className)) {
                $models[] = $className;
            }
        }

        return $models;
    }

    /**
     * {@inheritDoc}
     */
    public function analyzeModelRelationships(string $model): array
    {
        if (! class_exists($model)) {
            throw new SchemaAnalyzerException("Model {$model} does not exist");
        }

        $instance = new $model;
        $relationships = [];

        // Detect common relationship methods
        $methods = get_class_methods($instance);
        $relationshipMethods = array_filter($methods, fn ($method) => match ($method) {
            'hasMany', 'hasOne', 'belongsTo', 'belongsToMany',
            'hasManyThrough', 'hasOneThrough' => true,
            default => str_ends_with($method, 'Many') ||
                       str_ends_with($method, 'One') ||
                       str_ends_with($method, 'Through'),
        });

        foreach ($relationshipMethods as $method) {
            try {
                $relation = $instance->$method();
                $relationships[$method] = get_class($relation);
            } catch (\Throwable $e) {
                // Skip invalid relationships
            }
        }

        return $relationships;
    }

    /**
     * Get columns for a table based on driver.
     *
     * @return array<int, mixed>
     */
    protected function getColumnsForDriver(
        ConnectionInterface $connection,
        string $driver,
        string $wrappedTable,
        string $table
    ): array {
        return match ($driver) {
            'mysql' => $connection->select("DESCRIBE {$wrappedTable}"),
            'pgsql' => $connection->select(
                'SELECT column_name, data_type, is_nullable, column_default '
                .'FROM information_schema.columns '
                .'WHERE table_schema = ? AND table_name = ? ORDER BY ordinal_position',
                ['public', $table]
            ),
            'sqlite' => $connection->select("PRAGMA table_info({$wrappedTable})"),
            'sqlsrv' => $connection->select(
                'SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT '
                .'FROM INFORMATION_SCHEMA.COLUMNS '
                .'WHERE TABLE_NAME = ? ORDER BY ORDINAL_POSITION',
                [$table]
            ),
            default => throw new SchemaAnalyzerException('Unsupported database driver: '.$driver),
        };
    }

    /**
     * Get indexes for a table based on driver.
     *
     * @return array<int, mixed>
     */
    protected function getIndexesForDriver(
        ConnectionInterface $connection,
        string $driver,
        string $wrappedTable,
        string $table
    ): array {
        return match ($driver) {
            'mysql' => $connection->select("SHOW INDEX FROM {$wrappedTable}"),
            'pgsql' => $connection->select(
                'SELECT indexname, indexdef FROM pg_indexes WHERE schemaname = ? AND tablename = ?',
                ['public', $table]
            ),
            'sqlite' => $connection->select("PRAGMA index_list({$wrappedTable})"),
            'sqlsrv' => $connection->select(
                'SELECT i.name AS index_name, i.is_unique, i.type_desc '
                .'FROM sys.indexes i '
                .'INNER JOIN sys.objects o ON i.object_id = o.object_id '
                .'WHERE o.name = ? AND i.is_primary_key = 0',
                [$table]
            ),
            default => throw new SchemaAnalyzerException('Unsupported database driver: '.$driver),
        };
    }

    /**
     * Normalize column definitions across drivers.
     *
     * @param  array<int, mixed>  $columns
     * @param  array<int, string>  $primaryKeys
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeColumns(array $columns, string $driver, array $primaryKeys): array
    {
        return array_map(function ($column) use ($driver, $primaryKeys) {
            $data = is_object($column) ? get_object_vars($column) : (array) $column;

            return match ($driver) {
                'mysql' => [
                    'Field' => $data['Field'] ?? null,
                    'Type' => $data['Type'] ?? null,
                    'Null' => $data['Null'] ?? null,
                    'Key' => $data['Key'] ?? null,
                    'Default' => $data['Default'] ?? null,
                ],
                'pgsql' => [
                    'Field' => $data['column_name'] ?? null,
                    'Type' => $data['data_type'] ?? null,
                    'Null' => ($data['is_nullable'] ?? '') === 'YES' ? 'YES' : 'NO',
                    'Key' => in_array(($data['column_name'] ?? ''), $primaryKeys, true) ? 'PRI' : '',
                    'Default' => $data['column_default'] ?? null,
                ],
                'sqlite' => [
                    'Field' => $data['name'] ?? null,
                    'Type' => $data['type'] ?? null,
                    'Null' => (int) ($data['notnull'] ?? 0) === 0 ? 'YES' : 'NO',
                    'Key' => (int) ($data['pk'] ?? 0) > 0 ? 'PRI' : '',
                    'Default' => $data['dflt_value'] ?? null,
                ],
                'sqlsrv' => [
                    'Field' => $data['COLUMN_NAME'] ?? null,
                    'Type' => $data['DATA_TYPE'] ?? null,
                    'Null' => ($data['IS_NULLABLE'] ?? '') === 'YES' ? 'YES' : 'NO',
                    'Key' => in_array(($data['COLUMN_NAME'] ?? ''), $primaryKeys, true) ? 'PRI' : '',
                    'Default' => $data['COLUMN_DEFAULT'] ?? null,
                ],
                default => [
                    'Field' => $data['Field'] ?? null,
                    'Type' => $data['Type'] ?? null,
                    'Null' => $data['Null'] ?? null,
                    'Key' => $data['Key'] ?? null,
                    'Default' => $data['Default'] ?? null,
                ],
            };
        }, $columns);
    }

    /**
     * Resolve primary keys for a table.
     *
     * @param  array<int, mixed>  $columns
     * @return array<int, string>
     */
    protected function getPrimaryKeysForDriver(
        ConnectionInterface $connection,
        string $driver,
        string $table,
        array $columns
    ): array {
        if ($driver === 'mysql') {
            return array_values(array_filter(array_map(function ($column) {
                $data = is_object($column) ? get_object_vars($column) : (array) $column;

                return ($data['Key'] ?? null) === 'PRI' ? ($data['Field'] ?? null) : null;
            }, $columns)));
        }

        if ($driver === 'sqlite') {
            return array_values(array_filter(array_map(function ($column) {
                $data = is_object($column) ? get_object_vars($column) : (array) $column;

                return (int) ($data['pk'] ?? 0) > 0 ? ($data['name'] ?? null) : null;
            }, $columns)));
        }

        if ($driver === 'pgsql') {
            $rows = $connection->select(
                'SELECT kcu.column_name '
                .'FROM information_schema.table_constraints tc '
                .'JOIN information_schema.key_column_usage kcu '
                .'ON tc.constraint_name = kcu.constraint_name '
                .'AND tc.table_schema = kcu.table_schema '
                .'WHERE tc.constraint_type = ? AND tc.table_schema = ? AND tc.table_name = ?',
                ['PRIMARY KEY', 'public', $table]
            );

            return array_values(array_filter(array_map(function ($row) {
                $data = is_object($row) ? get_object_vars($row) : (array) $row;

                return $data['column_name'] ?? null;
            }, $rows)));
        }

        if ($driver === 'sqlsrv') {
            $rows = $connection->select(
                'SELECT kcu.COLUMN_NAME '
                .'FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc '
                .'JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE kcu '
                .'ON tc.CONSTRAINT_NAME = kcu.CONSTRAINT_NAME '
                .'WHERE tc.CONSTRAINT_TYPE = ? AND tc.TABLE_NAME = ?',
                ['PRIMARY KEY', $table]
            );

            return array_values(array_filter(array_map(function ($row) {
                $data = is_object($row) ? get_object_vars($row) : (array) $row;

                return $data['COLUMN_NAME'] ?? null;
            }, $rows)));
        }

        return [];
    }
}
