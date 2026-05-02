<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;
use Jemgdevp\Domo\Tests\TestCase;

class AiDriverTest extends TestCase
{
    public function test_anthropic_driver_can_be_instantiated(): void
    {
        $driver = new AnthropicDriver;
        $this->assertInstanceOf(AnthropicDriver::class, $driver);
    }

    public function test_openai_driver_can_be_instantiated(): void
    {
        $driver = new OpenAIDriver;
        $this->assertInstanceOf(OpenAIDriver::class, $driver);
    }
}
