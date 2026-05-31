<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\AI;

use Anthropic\Client;
use Anthropic\Core\Exceptions\AnthropicException;
use Anthropic\Messages\TextBlock;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Exceptions\AiDriverException;
use Jemgdevp\Domo\Services\AI\Concerns\BuildsPrompts;
use Throwable;

class AnthropicDriver implements AiDriverInterface
{
    use BuildsPrompts;

    /**
     * Default Anthropic model used when none is configured.
     */
    public const DEFAULT_MODEL = 'claude-sonnet-4-5';

    /**
     * Maximum tokens requested per message.
     */
    protected const MAX_TOKENS = 4096;

    /**
     * Lazily-constructed Anthropic SDK client.
     */
    protected ?Client $client = null;

    /**
     * @param  string|null  $apiKey  Anthropic API key; falls back to config when null.
     * @param  string|null  $model  Model id; falls back to config/default when null.
     * @param  string|null  $baseUrl  Custom API endpoint; falls back to config, then the official URL.
     */
    public function __construct(
        protected ?string $apiKey = null,
        protected ?string $model = null,
        protected ?string $baseUrl = null
    ) {}

    /**
     * {@inheritDoc}
     */
    public function analyzeSchema(array $schema): array
    {
        $response = $this->message($this->buildSchemaAnalysisPrompt($schema));

        return $this->decodeJsonResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function generateMigration(array $analysis): string
    {
        $response = $this->message($this->buildMigrationPrompt($analysis));

        return $this->stripCodeFences($response);
    }

    /**
     * {@inheritDoc}
     */
    public function suggestRelationships(array $models): array
    {
        $response = $this->message($this->buildRelationshipsPrompt($models));

        return $this->decodeJsonResponse($response);
    }

    /**
     * Send a single-turn message request and return the concatenated text blocks.
     *
     * @throws AiDriverException
     */
    protected function message(string $prompt): string
    {
        try {
            $message = $this->client()->messages->create(
                maxTokens: self::MAX_TOKENS,
                messages: [
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
                model: $this->resolveModel(),
                system: 'You are an expert Laravel and database assistant. Follow the user instructions about output format precisely.',
            );
        } catch (AnthropicException $exception) {
            throw new AiDriverException(
                'Anthropic request failed: '.$exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        } catch (Throwable $exception) {
            throw new AiDriverException(
                'Unexpected Anthropic driver error: '.$exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }

        $text = '';

        foreach ($message->content as $block) {
            if ($block instanceof TextBlock) {
                $text .= $block->text;
            }
        }

        if (trim($text) === '') {
            throw new AiDriverException('Anthropic returned an empty response.');
        }

        return $text;
    }

    /**
     * Resolve the lazily-built Anthropic client, validating the API key.
     *
     * @throws AiDriverException
     */
    protected function client(): Client
    {
        if ($this->client instanceof Client) {
            return $this->client;
        }

        $apiKey = $this->resolveApiKey();

        if ($apiKey === null || $apiKey === '') {
            throw new AiDriverException('Anthropic API key not configured.');
        }

        return $this->client = new Client(apiKey: $apiKey, baseUrl: $this->resolveBaseUrl());
    }

    /**
     * Resolve the API key from the constructor or package config.
     */
    protected function resolveApiKey(): ?string
    {
        if ($this->apiKey !== null && $this->apiKey !== '') {
            return $this->apiKey;
        }

        $configured = config('domo.providers.anthropic.api_key');

        return is_string($configured) ? $configured : null;
    }

    /**
     * Resolve the model from the constructor, config, or default constant.
     */
    protected function resolveModel(): string
    {
        if ($this->model !== null && $this->model !== '') {
            return $this->model;
        }

        $configured = config('domo.providers.anthropic.model');

        return is_string($configured) && $configured !== '' ? $configured : self::DEFAULT_MODEL;
    }

    /**
     * Resolve the API endpoint from the constructor or package config.
     */
    protected function resolveBaseUrl(): ?string
    {
        if ($this->baseUrl !== null && $this->baseUrl !== '') {
            return $this->baseUrl;
        }

        $configured = config('domo.providers.anthropic.base_url');

        return is_string($configured) && $configured !== '' ? $configured : null;
    }
}
