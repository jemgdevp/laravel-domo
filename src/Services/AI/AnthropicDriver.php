<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\AI;

use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Exceptions\AiDriverException;

class AnthropicDriver implements AiDriverInterface
{
    /**
     * @param string|null $apiKey
     */
    public function __construct(
        protected ?string $apiKey = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function analyzeSchema(array $schema): array
    {
        // TODO: Implement Anthropic API integration
        throw new AiDriverException('AnthropicDriver not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function generateMigration(array $analysis): string
    {
        // TODO: Implement Anthropic API integration
        throw new AiDriverException('AnthropicDriver not yet implemented');
    }

    /**
     * @inheritDoc
     */
    public function suggestRelationships(array $models): array
    {
        // TODO: Implement Anthropic API integration
        throw new AiDriverException('AnthropicDriver not yet implemented');
    }
}
