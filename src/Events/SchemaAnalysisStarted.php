<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when schema analysis is started.
 */
class SchemaAnalysisStarted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $target
    ) {}
}
