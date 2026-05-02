<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array analyzeSchema(array $schema)
 * @method static string generateMigration(array $analysis)
 * @method static array suggestRelationships(array $models)
 *
 * @see \Jemgdevp\Domo\Services\AI\AnthropicDriver
 * @see \Jemgdevp\Domo\Services\AI\OpenAIDriver
 */
class Domo extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return \Jemgdevp\Domo\Contracts\AiDriverInterface::class;
    }
}
