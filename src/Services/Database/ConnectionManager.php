<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Database;

use Illuminate\Database\Connection;
use Illuminate\Support\Facades\DB;

/**
 * Database connection manager for Laravel Domo.
 *
 * Handles database connections, retrieves schema information,
 * and provides utilities for database operations.
 */
class ConnectionManager
{
    /**
     * The database connection instance.
     */
    protected Connection $connection;

    /**
     * Create a new connection manager instance.
     *
     * @param string|null $connection
     */
    public function __construct(
        protected ?string $connection = null
    ) {
        $this->connection = DB::connection($connection);
    }

    /**
     * Get the database connection.
     *
     * @return Connection
     */
    public function getConnection(): Connection
    {
        return $this->connection;
    }

    /**
     * Get the database driver name.
     *
     * @return string
     */
    public function getDriverName(): string
    {
        return $this->connection->getDriverName();
    }

    /**
     * Get the database name.
     *
     * @return string
     */
    public function getDatabaseName(): string
    {
        return $this->connection->getDatabaseName();
    }

    /**
     * Get the table prefix.
     *
     * @return string
     */
    public function getTablePrefix(): string
    {
        return $this->connection->getTablePrefix();
    }

    /**
     * Check if the connection is to a SQLite database.
     *
     * @return bool
     */
    public function isSqlite(): bool
    {
        return $this->getDriverName() === 'sqlite';
    }

    /**
     * Check if the connection is to a MySQL database.
     *
     * @return bool
     */
    public function isMysql(): bool
    {
        return $this->getDriverName() === 'mysql';
    }

    /**
     * Check if the connection is to a PostgreSQL database.
     *
     * @return bool
     */
    public function isPostgres(): bool
    {
        return $this->getDriverName() === 'pgsql';
    }
}
