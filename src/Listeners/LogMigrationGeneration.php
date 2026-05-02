<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Listeners;

use Jemgdevp\Domo\Events\MigrationGenerated;
use Illuminate\Support\Facades\Log;

/**
 * Listener for migration generation.
 *
 * Logs migration creation and notifies relevant services.
 */
class LogMigrationGeneration
{
    /**
     * Handle the event.
     *
     * @param MigrationGenerated $event
     * @return void
     */
    public function handle(MigrationGenerated $event): void
    {
        Log::info('Migration generated', [
            'name' => $event->name,
            'path' => $event->path,
        ]);
    }
}
