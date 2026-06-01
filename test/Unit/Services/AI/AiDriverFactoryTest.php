<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\AI;

use Jemgdevp\Domo\Services\AI\AiDriverFactory;
use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;
use Jemgdevp\Domo\Tests\TestCase;
use ReflectionProperty;

final class AiDriverFactoryTest extends TestCase
{
    public function test_make_without_arguments_uses_the_active_provider(): void
    {
        config()->set('domo.ai_driver', 'anthropic');

        $this->assertInstanceOf(AnthropicDriver::class, (new AiDriverFactory)->make());
    }

    public function test_make_resolves_a_named_provider(): void
    {
        $this->assertInstanceOf(AnthropicDriver::class, (new AiDriverFactory)->make('anthropic'));
        $this->assertInstanceOf(OpenAIDriver::class, (new AiDriverFactory)->make('openai'));
    }

    public function test_opencode_variant_uses_the_openai_compatible_driver(): void
    {
        // 'opencode' is a non-anthropic variant, so it speaks the OpenAI protocol.
        $this->assertInstanceOf(OpenAIDriver::class, (new AiDriverFactory)->make('opencode'));
    }

    public function test_unknown_provider_falls_back_to_the_default(): void
    {
        config()->set('domo.ai_driver', 'anthropic');

        $this->assertInstanceOf(AnthropicDriver::class, (new AiDriverFactory)->make('does-not-exist'));
    }

    public function test_make_applies_the_model_override(): void
    {
        $driver = (new AiDriverFactory)->make('openai', 'gpt-4o');

        $this->assertSame('gpt-4o', $this->readProperty($driver, 'model'));
    }

    public function test_make_falls_back_to_the_configured_model_when_no_override(): void
    {
        config()->set('domo.providers.opencode.model', 'deepseek-v4-pro');

        $driver = (new AiDriverFactory)->make('opencode');

        $this->assertSame('deepseek-v4-pro', $this->readProperty($driver, 'model'));
    }

    public function test_opencode_resolves_its_default_base_url(): void
    {
        $driver = (new AiDriverFactory)->make('opencode');

        $this->assertSame(
            'https://opencode.ai/zen/go/v1/',
            $this->readProperty($driver, 'baseUrl')
        );
    }

    public function test_available_providers_lists_configured_keys_in_order(): void
    {
        $this->assertSame(
            ['openai', 'anthropic', 'opencode'],
            (new AiDriverFactory)->availableProviders()
        );
    }

    private function readProperty(object $object, string $property): mixed
    {
        return (new ReflectionProperty($object, $property))->getValue($object);
    }
}
