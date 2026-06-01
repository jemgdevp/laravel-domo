<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\AI;

use Jemgdevp\Domo\Contracts\AiDriverInterface;

/**
 * Builds AI drivers from the `domo.providers` configuration.
 *
 * Centralises the provider → driver resolution so both the container binding
 * and the dashboard (per-analysis provider/model selection) share one path.
 */
class AiDriverFactory
{
    /**
     * Build an AI driver for the given provider key.
     *
     * @param  string|null  $provider  Provider key from config('domo.providers');
     *                                 null/empty uses config('domo.ai_driver').
     * @param  string|null  $model  Optional per-call model override.
     */
    public function make(?string $provider = null, ?string $model = null): AiDriverInterface
    {
        /** @var array<string, array<string, mixed>> $providers */
        $providers = (array) config('domo.providers', []);

        $default = (string) config('domo.ai_driver', 'openai');
        $key = ($provider !== null && $provider !== '') ? $provider : $default;

        $config = $providers[$key]
            ?? $providers[$default]
            ?? $providers['openai']
            ?? [];

        $apiKey = is_string($config['api_key'] ?? null) ? $config['api_key'] : null;
        $baseUrl = is_string($config['base_url'] ?? null) ? $config['base_url'] : null;
        $resolvedModel = ($model !== null && $model !== '')
            ? $model
            : (is_string($config['model'] ?? null) ? $config['model'] : null);

        // "anthropic" speaks the Anthropic API; every other variant
        // ("openai", "opencode", "groq", "ollama", ...) is OpenAI-compatible.
        return match ($config['variant'] ?? 'openai') {
            'anthropic' => new AnthropicDriver($apiKey, $resolvedModel, $baseUrl),
            default => new OpenAIDriver($apiKey, $resolvedModel, $baseUrl),
        };
    }

    /**
     * The configured provider keys, in definition order.
     *
     * @return array<int, string>
     */
    public function availableProviders(): array
    {
        return array_keys((array) config('domo.providers', []));
    }
}
