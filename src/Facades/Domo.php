<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Facades;

use Illuminate\Support\Facades\Facade;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;

/**
 * @method static array analyzeSchema(array $schema)
 * @method static string generateMigration(array $analysis)
 * @method static array suggestRelationships(array $models)
 *
 * @see AnthropicDriver
 * @see OpenAIDriver
 */
class Domo extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return AiDriverInterface::class;
    }
}
