<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Schema;

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
        $driver = $connection->getDriverName();

        return match ($driver) {
            'mysql' => DB::select('SHOW TABLES'),
            'pgsql' => DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public'"),
            'sqlite' => DB::select("SELECT name FROM sqlite_master WHERE type = 'table' AND name != 'sqlite_sequence'"),
            'sqlsrv' => DB::select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'"),
            default => throw new \RuntimeException("Unsupported database driver: {$driver}"),
        };
    }

    /**
     * {@inheritDoc}
     */
    public function getTableSchema(string $table): array
    {
        $columns = DB::select("DESCRIBE {$table}");
        $indexes = DB::select("SHOW INDEX FROM {$table}");

        return [
            'columns' => $columns,
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
}
