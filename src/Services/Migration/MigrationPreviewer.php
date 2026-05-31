<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Migration;

/**
 * Migration preview service.
 *
 * Provides preview functionality for generated migrations
 * before they are applied to the database.
 *
 * The previewer performs a lightweight, regex-based parse of the
 * migration PHP source (it does NOT execute the migration or touch
 * the database). Both the human readable operations and the generated
 * SQL are therefore approximations intended for quick inspection.
 */
class MigrationPreviewer
{
    /**
     * Map of Blueprint column methods to their representative SQL type.
     *
     * @var array<string, string>
     */
    protected array $columnTypeMap = [
        'id' => 'BIGINT UNSIGNED AUTO_INCREMENT',
        'bigIncrements' => 'BIGINT UNSIGNED AUTO_INCREMENT',
        'increments' => 'INTEGER UNSIGNED AUTO_INCREMENT',
        'bigInteger' => 'BIGINT',
        'integer' => 'INTEGER',
        'mediumInteger' => 'MEDIUMINT',
        'smallInteger' => 'SMALLINT',
        'tinyInteger' => 'TINYINT',
        'unsignedBigInteger' => 'BIGINT UNSIGNED',
        'unsignedInteger' => 'INTEGER UNSIGNED',
        'foreignId' => 'BIGINT UNSIGNED',
        'foreignUuid' => 'CHAR(36)',
        'string' => 'VARCHAR(255)',
        'char' => 'CHAR',
        'text' => 'TEXT',
        'mediumText' => 'MEDIUMTEXT',
        'longText' => 'LONGTEXT',
        'boolean' => 'TINYINT(1)',
        'date' => 'DATE',
        'dateTime' => 'DATETIME',
        'dateTimeTz' => 'DATETIME',
        'time' => 'TIME',
        'timestamp' => 'TIMESTAMP',
        'timestampTz' => 'TIMESTAMP',
        'year' => 'YEAR',
        'decimal' => 'DECIMAL',
        'double' => 'DOUBLE',
        'float' => 'FLOAT',
        'json' => 'JSON',
        'jsonb' => 'JSONB',
        'uuid' => 'CHAR(36)',
        'ulid' => 'CHAR(26)',
        'ipAddress' => 'VARCHAR(45)',
        'macAddress' => 'VARCHAR(17)',
        'enum' => 'ENUM',
        'set' => 'SET',
        'binary' => 'BLOB',
        'uuidMorphs' => 'CHAR(36)',
    ];

    /**
     * Preview a migration.
     *
     * Accepts either raw migration PHP source code or a path to an
     * existing migration file (the file contents are read in that case).
     *
     * @param  string  $migration  Migration PHP source or a path to a migration file.
     * @return array{sql: array<int, string>, operations: array<int, string>}
     */
    public function preview(string $migration): array
    {
        $parsed = $this->parse($migration);

        return [
            'sql' => $parsed['sql'],
            'operations' => $parsed['operations'],
        ];
    }

    /**
     * Get migration statistics.
     *
     * @param  string  $migration  Migration PHP source or a path to a migration file.
     * @return array{tables_affected: int, columns_added: int, columns_dropped: int, indexes_added: int}
     */
    public function getStatistics(string $migration): array
    {
        $parsed = $this->parse($migration);

        return $parsed['statistics'];
    }

    /**
     * Parse migration source code into operations, SQL and statistics.
     *
     * This is a best-effort, regex based parser. It never throws on
     * unparseable or empty input; it simply returns empty structures.
     *
     * @param  string  $migration  Migration PHP source or a path to a migration file.
     * @return array{
     *     operations: array<int, string>,
     *     sql: array<int, string>,
     *     statistics: array{tables_affected: int, columns_added: int, columns_dropped: int, indexes_added: int}
     * }
     */
    protected function parse(string $migration): array
    {
        $empty = [
            'operations' => [],
            'sql' => [],
            'statistics' => [
                'tables_affected' => 0,
                'columns_added' => 0,
                'columns_dropped' => 0,
                'indexes_added' => 0,
            ],
        ];

        $code = $this->resolveSource($migration);

        if ($code === '') {
            return $empty;
        }

        $operations = [];
        $sql = [];
        $tables = [];
        $columnsAdded = 0;
        $columnsDropped = 0;
        $indexesAdded = 0;

        foreach ($this->extractSchemaBlocks($code) as $block) {
            $type = $block['type'];
            $table = $block['table'];
            $body = $block['body'];

            $tables[$table] = true;

            if ($type === 'create') {
                $columns = $this->parseColumns($body);
                $indexes = $this->parseIndexes($body);

                $operations[] = sprintf('create table %s', $table);
                $columnsAdded += count($columns);
                $indexesAdded += count($indexes);

                foreach ($columns as $column) {
                    $operations[] = sprintf(
                        'add column %s %s',
                        $column['name'],
                        $column['type']
                    );
                }

                foreach ($indexes as $index) {
                    $operations[] = $index['operation'];
                }

                $sql = array_merge($sql, $this->buildCreateSql($table, $columns, $indexes));

                continue;
            }

            if ($type === 'table') {
                $columns = $this->parseColumns($body);
                $dropped = $this->parseDroppedColumns($body);
                $indexes = $this->parseIndexes($body);

                $operations[] = sprintf('alter table %s', $table);
                $columnsAdded += count($columns);
                $columnsDropped += count($dropped);
                $indexesAdded += count($indexes);

                foreach ($columns as $column) {
                    $operations[] = sprintf(
                        'add column %s %s',
                        $column['name'],
                        $column['type']
                    );
                    $sql[] = sprintf(
                        'ALTER TABLE `%s` ADD COLUMN `%s` %s;',
                        $table,
                        $column['name'],
                        $column['type']
                    );
                }

                foreach ($dropped as $name) {
                    $operations[] = sprintf('drop column %s', $name);
                    $sql[] = sprintf('ALTER TABLE `%s` DROP COLUMN `%s`;', $table, $name);
                }

                foreach ($indexes as $index) {
                    $operations[] = $index['operation'];

                    $columnList = $index['columns'] === []
                        ? ''
                        : ' (`'.implode('`, `', $index['columns']).'`)';

                    $sql[] = sprintf(
                        'ALTER TABLE `%s` ADD %s%s;',
                        $table,
                        $index['label'],
                        $columnList
                    );
                }

                continue;
            }

            if ($type === 'drop') {
                $operations[] = sprintf('drop table %s', $table);
                $sql[] = sprintf('DROP TABLE IF EXISTS `%s`;', $table);
            }
        }

        return [
            'operations' => $operations,
            'sql' => $sql,
            'statistics' => [
                'tables_affected' => count($tables),
                'columns_added' => $columnsAdded,
                'columns_dropped' => $columnsDropped,
                'indexes_added' => $indexesAdded,
            ],
        ];
    }

    /**
     * Resolve the migration source.
     *
     * If the given string is a path to an existing, readable file its
     * contents are returned; otherwise the string itself is treated as
     * the migration source code.
     *
     * @param  string  $migration  Migration source or file path.
     */
    protected function resolveSource(string $migration): string
    {
        $candidate = trim($migration);

        if ($candidate === '') {
            return '';
        }

        // A path will never contain a newline and should be reasonably short.
        if (
            ! str_contains($candidate, "\n")
            && strlen($candidate) <= 4096
            && is_file($candidate)
            && is_readable($candidate)
        ) {
            $contents = @file_get_contents($candidate);

            return $contents === false ? '' : $contents;
        }

        return $migration;
    }

    /**
     * Extract Schema::create / Schema::table / Schema::drop blocks.
     *
     * @param  string  $code  Migration source code.
     * @return array<int, array{type: string, table: string, body: string}>
     */
    protected function extractSchemaBlocks(string $code): array
    {
        $blocks = [];

        // Schema::create('table', function (...) { BODY });
        // Schema::table('table', function (...) { BODY });
        $pattern = '/Schema::(create|table)\s*\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*'
            .'function\s*\([^)]*\)\s*(?:use\s*\([^)]*\)\s*)?\{(.*?)\}\s*\)\s*;/s';

        if (preg_match_all($pattern, $code, $matches, PREG_SET_ORDER) !== false) {
            foreach ($matches as $match) {
                $blocks[] = [
                    'type' => strtolower($match[1]),
                    'table' => $match[2],
                    'body' => $match[3],
                ];
            }
        }

        // Schema::drop('table'); and Schema::dropIfExists('table');
        $dropPattern = '/Schema::drop(?:IfExists)?\s*\(\s*[\'"]([^\'"]+)[\'"]\s*\)/';

        if (preg_match_all($dropPattern, $code, $dropMatches) !== false) {
            foreach ($dropMatches[1] as $table) {
                $blocks[] = [
                    'type' => 'drop',
                    'table' => $table,
                    'body' => '',
                ];
            }
        }

        return $blocks;
    }

    /**
     * Parse column definitions from a Blueprint body.
     *
     * @param  string  $body  The closure body of a Schema block.
     * @return array<int, array{name: string, type: string, method: string}>
     */
    protected function parseColumns(string $body): array
    {
        $columns = [];

        // $table->method('name', ...) or $table->id()/timestamps() (no name).
        $pattern = '/\$table->([a-zA-Z]+)\s*\(([^;]*?)\)/s';

        if (preg_match_all($pattern, $body, $matches, PREG_SET_ORDER) === false) {
            return $columns;
        }

        foreach ($matches as $match) {
            $method = $match[1];
            $args = trim($match[2]);

            if (! array_key_exists($method, $this->columnTypeMap)) {
                continue;
            }

            foreach ($this->expandColumnMethod($method, $args) as $column) {
                $columns[] = $column;
            }
        }

        return $columns;
    }

    /**
     * Expand a single Blueprint column method into one or more columns.
     *
     * Handles convenience methods that create multiple columns such as
     * timestamps() or uuidMorphs().
     *
     * @param  string  $method  Blueprint method name.
     * @param  string  $args  Raw argument string (without surrounding parentheses).
     * @return array<int, array{name: string, type: string, method: string}>
     */
    protected function expandColumnMethod(string $method, string $args): array
    {
        $type = $this->columnTypeMap[$method];
        $name = $this->firstStringArgument($args);

        if ($method === 'id' && $name === null) {
            $name = 'id';
        }

        if ($name === null || $name === '') {
            return [];
        }

        if (in_array($method, ['uuidMorphs', 'morphs'], true)) {
            return [
                ['name' => $name.'_id', 'type' => $type, 'method' => $method],
                ['name' => $name.'_type', 'type' => 'VARCHAR(255)', 'method' => $method],
            ];
        }

        return [
            ['name' => $name, 'type' => $type, 'method' => $method],
        ];
    }

    /**
     * Parse dropped columns from a Blueprint body.
     *
     * Supports dropColumn('a'), dropColumn(['a', 'b']) and dropSoftDeletes()/
     * dropTimestamps() convenience helpers.
     *
     * @param  string  $body  The closure body of a Schema block.
     * @return array<int, string>
     */
    protected function parseDroppedColumns(string $body): array
    {
        $dropped = [];

        if (preg_match_all('/\$table->dropColumn\s*\(([^;]*?)\)/s', $body, $matches) !== false) {
            foreach ($matches[1] as $args) {
                if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $args, $names) !== false) {
                    foreach ($names[1] as $columnName) {
                        $dropped[] = $columnName;
                    }
                }
            }
        }

        if (preg_match_all('/\$table->(dropSoftDeletes|dropTimestamps|dropRememberToken)\s*\(/', $body, $helpers) !== false) {
            foreach ($helpers[1] as $helper) {
                $dropped = array_merge($dropped, match ($helper) {
                    'dropSoftDeletes' => ['deleted_at'],
                    'dropTimestamps' => ['created_at', 'updated_at'],
                    'dropRememberToken' => ['remember_token'],
                });
            }
        }

        return $dropped;
    }

    /**
     * Parse index definitions from a Blueprint body.
     *
     * @param  string  $body  The closure body of a Schema block.
     * @return array<int, array{operation: string, label: string, columns: array<int, string>}>
     */
    protected function parseIndexes(string $body): array
    {
        $indexes = [];

        $map = [
            'primary' => 'PRIMARY KEY',
            'unique' => 'UNIQUE INDEX',
            'index' => 'INDEX',
            'fullText' => 'FULLTEXT INDEX',
            'fulltext' => 'FULLTEXT INDEX',
            'spatialIndex' => 'SPATIAL INDEX',
        ];

        foreach ($map as $method => $label) {
            $pattern = '/\$table->'.preg_quote($method, '/').'\s*\(([^;]*?)\)/s';

            if (preg_match_all($pattern, $body, $matches) === false) {
                continue;
            }

            foreach ($matches[1] as $args) {
                $columns = [];

                if (preg_match_all('/[\'"]([^\'"]+)[\'"]/', $args, $names) !== false) {
                    $columns = $names[1];
                }

                $columnList = $columns === [] ? '' : ' ('.implode(', ', $columns).')';

                $indexes[] = [
                    'operation' => trim(sprintf('add %s%s', strtolower($label), $columnList)),
                    'label' => $label,
                    'columns' => $columns,
                ];
            }
        }

        return $indexes;
    }

    /**
     * Extract the first string literal argument from a raw argument string.
     *
     * @param  string  $args  Raw argument string (without surrounding parentheses).
     */
    protected function firstStringArgument(string $args): ?string
    {
        if (preg_match('/[\'"]([^\'"]+)[\'"]/', $args, $match) === 1) {
            return $match[1];
        }

        return null;
    }

    /**
     * Build representative CREATE TABLE SQL for a parsed table.
     *
     * @param  string  $table  Table name.
     * @param  array<int, array{name: string, type: string, method: string}>  $columns
     * @param  array<int, array{operation: string, label: string, columns: array<int, string>}>  $indexes
     * @return array<int, string>
     */
    protected function buildCreateSql(string $table, array $columns, array $indexes): array
    {
        $lines = [];

        foreach ($columns as $column) {
            $lines[] = sprintf('  `%s` %s', $column['name'], $column['type']);
        }

        foreach ($indexes as $index) {
            if ($index['columns'] === []) {
                continue;
            }

            $columnList = implode('`, `', $index['columns']);
            $lines[] = sprintf('  %s (`%s`)', $index['label'], $columnList);
        }

        if ($lines === []) {
            return [sprintf('CREATE TABLE `%s` ();', $table)];
        }

        return [
            sprintf("CREATE TABLE `%s` (\n%s\n);", $table, implode(",\n", $lines)),
        ];
    }
}
