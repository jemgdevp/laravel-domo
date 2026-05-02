<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when schema analysis is completed.
 */
class SchemaAnalysisCompleted
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param string $target
     * @param array<string, mixed> $results
     */
    public function __construct(
        public readonly string $target,
        public readonly array $results
    ) {
    }
}
