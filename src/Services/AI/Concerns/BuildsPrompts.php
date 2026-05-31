<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Services\AI\Concerns;

/**
 * Shared prompt-building and response-parsing logic for AI drivers.
 */
trait BuildsPrompts
{
    /**
     * Build the prompt asking the model to analyze a database schema.
     *
     * @param  array<string, mixed>  $schema
     */
    protected function buildSchemaAnalysisPrompt(array $schema): string
    {
        return <<<PROMPT
            You are a senior Laravel database architect. Analyze the following database schema
            and return insights and suggestions.

            Respond ONLY with a valid JSON object (no markdown, no commentary) using this shape:
            {
              "summary": "short overview of the schema",
              "insights": ["..."],
              "suggestions": ["..."],
              "warnings": ["..."]
            }

            Database schema (JSON):
            {$this->encodeContext($schema)}
            PROMPT;
    }

    /**
     * Build the prompt asking the model to generate a Laravel migration.
     *
     * @param  array<string, mixed>  $analysis
     */
    protected function buildMigrationPrompt(array $analysis): string
    {
        return <<<PROMPT
            You are a senior Laravel developer. Based on the following schema analysis,
            generate a single Laravel migration class.

            Return ONLY raw PHP code for the migration. Do not include explanations or
            markdown code fences. The code must start with "<?php" and use the modern
            anonymous-class migration style.

            Schema analysis (JSON):
            {$this->encodeContext($analysis)}
            PROMPT;
    }

    /**
     * Build the prompt asking the model to suggest Eloquent relationships.
     *
     * @param  array<array-key, mixed>  $models
     */
    protected function buildRelationshipsPrompt(array $models): string
    {
        return <<<PROMPT
            You are a senior Laravel developer. Based on the following models/tables,
            suggest the Eloquent relationships that should exist between them.

            Respond ONLY with a valid JSON object (no markdown, no commentary) using this shape:
            {
              "relationships": [
                {
                  "model": "User",
                  "method": "posts",
                  "type": "hasMany",
                  "related": "Post",
                  "foreign_key": "user_id",
                  "reason": "..."
                }
              ]
            }

            Models/tables (JSON):
            {$this->encodeContext($models)}
            PROMPT;
    }

    /**
     * Encode arbitrary context as pretty JSON for embedding in a prompt.
     *
     * @param  array<array-key, mixed>  $context
     */
    protected function encodeContext(array $context): string
    {
        $encoded = json_encode($context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $encoded === false ? '{}' : $encoded;
    }

    /**
     * Decode a model's textual response into an array.
     *
     * Strips optional markdown fences first. If the text is not valid JSON,
     * the raw text is wrapped under the "analysis" key.
     *
     * @return array<string, mixed>
     */
    protected function decodeJsonResponse(string $text): array
    {
        $cleaned = $this->stripCodeFences($text);

        /** @var mixed $decoded */
        $decoded = json_decode($cleaned, true);

        if (is_array($decoded)) {
            /** @var array<string, mixed> $decoded */
            return $decoded;
        }

        return ['analysis' => trim($text)];
    }

    /**
     * Remove surrounding markdown code fences (```json / ```php / ```) from a response.
     */
    protected function stripCodeFences(string $text): string
    {
        $trimmed = trim($text);

        if (! str_starts_with($trimmed, '```')) {
            return $trimmed;
        }

        // Drop the opening fence line (which may carry a language hint).
        $withoutOpening = preg_replace('/^```[a-zA-Z0-9]*\s*\n?/', '', $trimmed);
        $withoutOpening ??= $trimmed;

        // Drop the closing fence.
        $withoutClosing = preg_replace('/\n?```\s*$/', '', $withoutOpening);
        $withoutClosing ??= $withoutOpening;

        return trim($withoutClosing);
    }
}
