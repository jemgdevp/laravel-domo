<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\AI;

use Jemgdevp\Domo\Exceptions\AiDriverException;
use Jemgdevp\Domo\Exceptions\DomoException;
use Jemgdevp\Domo\Services\AI\OpenAIDriver;
use Jemgdevp\Domo\Tests\TestCase;
use OpenAI\Responses\Chat\CreateResponse;

/**
 * Test double that bypasses the real OpenAI HTTP transport.
 *
 * The protected {@see OpenAIDriver::chat()} method is the single seam through
 * which the driver talks to the SDK client. Overriding it lets the tests drive
 * the real public methods (analyzeSchema/generateMigration/suggestRelationships)
 * and exercise their JSON-decoding / code-fence-stripping logic without any
 * network access. The canned text is produced from the OpenAI SDK's own
 * CreateResponse::fake() helper to stay coupled to the real response shape.
 */
final class FakeChatOpenAIDriver extends OpenAIDriver
{
    /**
     * @var array<int, string>
     */
    public array $prompts = [];

    public function __construct(private readonly string $cannedResponse)
    {
        parent::__construct(apiKey: 'test-key');
    }

    protected function chat(string $prompt): string
    {
        $this->prompts[] = $prompt;

        return $this->cannedResponse;
    }
}

final class OpenAIDriverTest extends TestCase
{
    public function test_constructor_can_be_instantiated_lazily_without_arguments(): void
    {
        $driver = new OpenAIDriver;

        $this->assertInstanceOf(OpenAIDriver::class, $driver);
    }

    public function test_default_model_constant_matches_expected_value(): void
    {
        $this->assertSame('gpt-4o-mini', OpenAIDriver::DEFAULT_MODEL);
    }

    public function test_analyze_schema_throws_ai_driver_exception_when_api_key_missing(): void
    {
        config()->set('domo.providers.openai.api_key', null);

        $driver = new OpenAIDriver(apiKey: null);

        $this->expectException(AiDriverException::class);
        $this->expectExceptionMessage('OpenAI API key not configured.');

        $driver->analyzeSchema(['tables' => []]);
    }

    public function test_ai_driver_exception_is_a_domo_exception(): void
    {
        config()->set('domo.providers.openai.api_key', null);

        $driver = new OpenAIDriver(apiKey: '');

        try {
            $driver->generateMigration(['summary' => 'noop']);
            $this->fail('Expected AiDriverException was not thrown.');
        } catch (AiDriverException $exception) {
            $this->assertInstanceOf(DomoException::class, $exception);
        }
    }

    public function test_analyze_schema_parses_json_object_response(): void
    {
        $content = $this->fakeChatContent(json_encode([
            'summary' => 'Three normalized tables.',
            'insights' => ['users drives most relations'],
            'suggestions' => ['add index on posts.user_id'],
            'warnings' => [],
        ], JSON_THROW_ON_ERROR));

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->analyzeSchema(['tables' => ['users', 'posts']]);

        $this->assertSame('Three normalized tables.', $result['summary']);
        $this->assertSame(['users drives most relations'], $result['insights']);
        $this->assertSame(['add index on posts.user_id'], $result['suggestions']);
        $this->assertSame([], $result['warnings']);
    }

    public function test_analyze_schema_strips_json_code_fences_before_decoding(): void
    {
        $content = $this->fakeChatContent("```json\n{\"summary\":\"fenced\",\"insights\":[]}\n```");

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->analyzeSchema(['tables' => []]);

        $this->assertSame('fenced', $result['summary']);
        $this->assertSame([], $result['insights']);
    }

    public function test_analyze_schema_wraps_non_json_response_under_analysis_key(): void
    {
        $content = $this->fakeChatContent('This is plain prose, not JSON.');

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->analyzeSchema(['tables' => []]);

        $this->assertSame(['analysis' => 'This is plain prose, not JSON.'], $result);
    }

    public function test_suggest_relationships_parses_json_object_response(): void
    {
        $content = $this->fakeChatContent(json_encode([
            'relationships' => [
                [
                    'model' => 'User',
                    'method' => 'posts',
                    'type' => 'hasMany',
                    'related' => 'Post',
                    'foreign_key' => 'user_id',
                    'reason' => 'posts.user_id references users.id',
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->suggestRelationships(['User', 'Post']);

        $this->assertCount(1, $result['relationships']);
        $this->assertSame('hasMany', $result['relationships'][0]['type']);
        $this->assertSame('user_id', $result['relationships'][0]['foreign_key']);
    }

    public function test_generate_migration_strips_php_code_fences(): void
    {
        $migration = "<?php\n\nreturn new class extends Migration {};";
        $content = $this->fakeChatContent("```php\n{$migration}\n```");

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->generateMigration(['summary' => 'create users table']);

        $this->assertSame($migration, $result);
        $this->assertStringStartsWith('<?php', $result);
        $this->assertStringNotContainsString('```', $result);
    }

    public function test_generate_migration_returns_trimmed_unfenced_code(): void
    {
        $migration = "<?php\n\nreturn new class extends Migration {};";
        $content = $this->fakeChatContent("\n\n{$migration}\n\n");

        $driver = new FakeChatOpenAIDriver($content);

        $result = $driver->generateMigration(['summary' => 'noop']);

        $this->assertSame($migration, $result);
    }

    /**
     * Build assistant text the way the real OpenAI SDK would expose it,
     * using the SDK's own fake response helper.
     */
    private function fakeChatContent(string $content): string
    {
        $response = CreateResponse::fake([
            'choices' => [
                [
                    'message' => [
                        'content' => $content,
                    ],
                ],
            ],
        ]);

        return (string) $response->choices[0]->message->content;
    }
}
