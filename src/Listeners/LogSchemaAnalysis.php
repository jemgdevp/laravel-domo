<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Listeners;

use Jemgdevp\Domo\Events\SchemaAnalysisCompleted;
use Illuminate\Support\Facades\Log;

/**
 * Listener for schema analysis completion.
 *
 * Logs analysis results and triggers related actions.
 */
class LogSchemaAnalysis
{
    /**
     * Handle the event.
     *
     * @param SchemaAnalysisCompleted $event
     * @return void
     */
    public function handle(SchemaAnalysisCompleted $event): void
    {
        Log::info('Schema analysis completed', [
            'target' => $event->target,
            'results' => $event->results,
        ]);
    }
}
