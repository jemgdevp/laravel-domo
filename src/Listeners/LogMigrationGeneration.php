<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Listeners;

use Illuminate\Support\Facades\Log;
use Jemgdevp\Domo\Events\MigrationGenerated;

/**
 * Listener for migration generation.
 *
 * Logs migration creation and notifies relevant services.
 */
class LogMigrationGeneration
{
    /**
     * Handle the event.
     */
    public function handle(MigrationGenerated $event): void
    {
        Log::info('Migration generated', [
            'name' => $event->name,
            'path' => $event->path,
        ]);
    }
}
