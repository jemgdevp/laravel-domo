<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\AI;

use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Exceptions\AiDriverException;
use Jemgdevp\Domo\Services\AI\Concerns\BuildsPrompts;
use OpenAI;
use OpenAI\Client;
use OpenAI\Exceptions\ErrorException;
use OpenAI\Exceptions\TransporterException;
use Throwable;

class OpenAIDriver implements AiDriverInterface
{
    use BuildsPrompts;

    /**
     * Default OpenAI chat model used when none is configured.
     */
    public const DEFAULT_MODEL = 'gpt-4o-mini';

    /**
     * Lazily-constructed OpenAI SDK client.
     */
    protected ?Client $client = null;

    /**
     * @param  string|null  $apiKey  OpenAI API key; falls back to config when null.
     * @param  string|null  $model  Chat model id; falls back to config/default when null.
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
        $response = $this->chat($this->buildSchemaAnalysisPrompt($schema));

        return $this->decodeJsonResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function generateMigration(array $analysis): string
    {
        $response = $this->chat($this->buildMigrationPrompt($analysis));

        return $this->stripCodeFences($response);
    }

    /**
     * {@inheritDoc}
     */
    public function suggestRelationships(array $models): array
    {
        $response = $this->chat($this->buildRelationshipsPrompt($models));

        return $this->decodeJsonResponse($response);
    }

    /**
     * Send a single-turn chat completion request and return the assistant text.
     *
     * @throws AiDriverException
     */
    protected function chat(string $prompt): string
    {
        try {
            $response = $this->client()->chat()->create([
                'model' => $this->resolveModel(),
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an expert Laravel and database assistant. Follow the user instructions about output format precisely.',
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt,
                    ],
                ],
            ]);
        } catch (ErrorException|TransporterException $exception) {
            throw new AiDriverException(
                'OpenAI request failed: '.$exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        } catch (Throwable $exception) {
            throw new AiDriverException(
                'Unexpected OpenAI driver error: '.$exception->getMessage(),
                (int) $exception->getCode(),
                $exception
            );
        }

        $content = $response->choices[0]->message->content ?? null;

        if ($content === null || trim($content) === '') {
            throw new AiDriverException('OpenAI returned an empty response.');
        }

        return $content;
    }

    /**
     * Resolve the lazily-built OpenAI client, validating the API key.
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
            throw new AiDriverException('OpenAI API key not configured.');
        }

        $baseUrl = $this->resolveBaseUrl();

        if ($baseUrl === null) {
            return $this->client = OpenAI::client($apiKey);
        }

        return $this->client = OpenAI::factory()
            ->withApiKey($apiKey)
            ->withBaseUri($baseUrl)
            ->make();
    }

    /**
     * Resolve the API key from the constructor or package config.
     */
    protected function resolveApiKey(): ?string
    {
        if ($this->apiKey !== null && $this->apiKey !== '') {
            return $this->apiKey;
        }

        $configured = config('domo.providers.openai.api_key');

        return is_string($configured) ? $configured : null;
    }

    /**
     * Resolve the chat model from the constructor, config, or default constant.
     */
    protected function resolveModel(): string
    {
        if ($this->model !== null && $this->model !== '') {
            return $this->model;
        }

        $configured = config('domo.providers.openai.model');

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

        $configured = config('domo.providers.openai.base_url');

        return is_string($configured) && $configured !== '' ? $configured : null;
    }
}
