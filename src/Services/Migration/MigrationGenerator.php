<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\Migration;

use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Filesystem\Filesystem;

/**
 * Migration generator service.
 *
 * Generates Laravel migration files based on schema analysis
 * and AI suggestions.
 */
class MigrationGenerator
{
    /**
     * The migration creator instance.
     */
    protected MigrationCreator $creator;

    /**
     * The filesystem instance.
     */
    protected Filesystem $files;

    /**
     * Create a new migration generator instance.
     *
     * @param MigrationCreator $creator
     * @param Filesystem $files
     */
    public function __construct(
        MigrationCreator $creator,
        Filesystem $files
    ) {
        $this->creator = $creator;
        $this->files = $files;
    }

    /**
     * Generate a migration file.
     *
     * @param string $name
     * @param string $table
     * @param string $method
     * @return string
     */
    public function generate(
        string $name,
        string $table,
        string $method = 'create'
    ): string {
        return $this->creator->create($name, $this->getMigrationsPath(), $table, $method);
    }

    /**
     * Get the migrations path.
     *
     * @return string
     */
    protected function getMigrationsPath(): string
    {
        return database_path('migrations');
    }

    /**
     * Get pending migrations.
     *
     * @return array<string>
     */
    public function getPendingMigrations(): array
    {
        $migrationsPath = $this->getMigrationsPath();
        
        if (! $this->files->exists($migrationsPath)) {
            return [];
        }

        return collect($this->files->files($migrationsPath))
            ->map(fn($file) => $file->getFilename())
            ->toArray();
    }
}
