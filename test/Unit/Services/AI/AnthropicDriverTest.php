<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\AI;

use Jemgdevp\Domo\Exceptions\AiDriverException;
use Jemgdevp\Domo\Exceptions\DomoException;
use Jemgdevp\Domo\Services\AI\AnthropicDriver;
use Jemgdevp\Domo\Tests\TestCase;

/**
 * Test double that bypasses the real Anthropic HTTP transport.
 *
 * The Anthropic SDK ships no response/client fake helper and the driver builds
 * its client internally, so the protected {@see AnthropicDriver::message()}
 * seam is overridden to return canned assistant text. This drives the real
 * public methods (analyzeSchema/generateMigration/suggestRelationships) and
 * exercises their JSON-decoding / code-fence-stripping logic without any
 * network access.
 */
final class FakeMessageAnthropicDriver extends AnthropicDriver
{
    /**
     * @var array<int, string>
     */
    public array $prompts = [];

    public function __construct(private readonly string $cannedResponse)
    {
        parent::__construct(apiKey: 'test-key');
    }

    protected function message(string $prompt): string
    {
        $this->prompts[] = $prompt;

        return $this->cannedResponse;
    }
}

final class AnthropicDriverTest extends TestCase
{
    public function test_constructor_can_be_instantiated_lazily_without_arguments(): void
    {
        $driver = new AnthropicDriver;

        $this->assertInstanceOf(AnthropicDriver::class, $driver);
    }

    public function test_default_model_constant_matches_expected_value(): void
    {
        $this->assertSame('claude-sonnet-4-5', AnthropicDriver::DEFAULT_MODEL);
    }

    public function test_analyze_schema_throws_ai_driver_exception_when_api_key_missing(): void
    {
        config()->set('domo.providers.anthropic.api_key', null);

        $driver = new AnthropicDriver(apiKey: null);

        $this->expectException(AiDriverException::class);
        $this->expectExceptionMessage('Anthropic API key not configured.');

        $driver->analyzeSchema(['tables' => []]);
    }

    public function test_ai_driver_exception_is_a_domo_exception(): void
    {
        config()->set('domo.providers.anthropic.api_key', null);

        $driver = new AnthropicDriver(apiKey: '');

        try {
            $driver->suggestRelationships(['User']);
            $this->fail('Expected AiDriverException was not thrown.');
        } catch (AiDriverException $exception) {
            $this->assertInstanceOf(DomoException::class, $exception);
        }
    }

    public function test_analyze_schema_parses_json_object_response(): void
    {
        $content = json_encode([
            'summary' => 'Normalized schema.',
            'insights' => ['users is central'],
            'suggestions' => ['add soft deletes'],
            'warnings' => ['posts lacks index'],
        ], JSON_THROW_ON_ERROR);

        $driver = new FakeMessageAnthropicDriver($content);

        $result = $driver->analyzeSchema(['tables' => ['users', 'posts']]);

        $this->assertSame('Normalized schema.', $result['summary']);
        $this->assertSame(['users is central'], $result['insights']);
        $this->assertSame(['add soft deletes'], $result['suggestions']);
        $this->assertSame(['posts lacks index'], $result['warnings']);
    }

    public function test_analyze_schema_strips_json_code_fences_before_decoding(): void
    {
        $driver = new FakeMessageAnthropicDriver("```json\n{\"summary\":\"fenced\",\"insights\":[]}\n```");

        $result = $driver->analyzeSchema(['tables' => []]);

        $this->assertSame('fenced', $result['summary']);
        $this->assertSame([], $result['insights']);
    }

    public function test_analyze_schema_wraps_non_json_response_under_analysis_key(): void
    {
        $driver = new FakeMessageAnthropicDriver('Plain prose, not JSON.');

        $result = $driver->analyzeSchema(['tables' => []]);

        $this->assertSame(['analysis' => 'Plain prose, not JSON.'], $result);
    }

    public function test_suggest_relationships_parses_json_object_response(): void
    {
        $content = json_encode([
            'relationships' => [
                [
                    'model' => 'Post',
                    'method' => 'author',
                    'type' => 'belongsTo',
                    'related' => 'User',
                    'foreign_key' => 'user_id',
                    'reason' => 'posts.user_id references users.id',
                ],
            ],
        ], JSON_THROW_ON_ERROR);

        $driver = new FakeMessageAnthropicDriver($content);

        $result = $driver->suggestRelationships(['User', 'Post']);

        $this->assertCount(1, $result['relationships']);
        $this->assertSame('belongsTo', $result['relationships'][0]['type']);
        $this->assertSame('User', $result['relationships'][0]['related']);
    }

    public function test_generate_migration_strips_php_code_fences(): void
    {
        $migration = "<?php\n\nreturn new class extends Migration {};";

        $driver = new FakeMessageAnthropicDriver("```php\n{$migration}\n```");

        $result = $driver->generateMigration(['summary' => 'create users table']);

        $this->assertSame($migration, $result);
        $this->assertStringStartsWith('<?php', $result);
        $this->assertStringNotContainsString('```', $result);
    }

    public function test_generate_migration_returns_trimmed_unfenced_code(): void
    {
        $migration = "<?php\n\nreturn new class extends Migration {};";

        $driver = new FakeMessageAnthropicDriver("\n\n{$migration}\n\n");

        $result = $driver->generateMigration(['summary' => 'noop']);

        $this->assertSame($migration, $result);
    }
}
