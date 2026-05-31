<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;
use Jemgdevp\Domo\Tests\TestCase;

class ProviderResolutionTest extends TestCase
{
    public function test_default_provider_resolves_openai_driver(): void
    {
        config()->set('domo.ai_driver', 'openai');

        $this->assertInstanceOf(OpenAIDriver::class, app(AiDriverInterface::class));
    }

    public function test_anthropic_provider_resolves_anthropic_driver(): void
    {
        config()->set('domo.ai_driver', 'anthropic');

        $this->assertInstanceOf(AnthropicDriver::class, app(AiDriverInterface::class));
    }

    public function test_custom_provider_resolves_by_variant(): void
    {
        config()->set('domo.providers.groq', [
            'variant' => 'openai',
            'api_key' => 'test-key',
            'model' => 'llama-3.3-70b-versatile',
            'base_url' => 'https://api.groq.com/openai/v1',
        ]);
        config()->set('domo.ai_driver', 'groq');

        $this->assertInstanceOf(OpenAIDriver::class, app(AiDriverInterface::class));
    }

    public function test_unknown_provider_falls_back_to_openai_driver(): void
    {
        config()->set('domo.ai_driver', 'does-not-exist');

        $this->assertInstanceOf(OpenAIDriver::class, app(AiDriverInterface::class));
    }
}
