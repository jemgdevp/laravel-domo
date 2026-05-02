<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event fired when a migration is generated.
 */
class MigrationGenerated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly string $name,
        public readonly string $path
    ) {}
}
