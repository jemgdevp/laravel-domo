<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Jemgdevp\Domo\Contracts\AiDriverInterface;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Exceptions\AiDriverException;
use Jemgdevp\Domo\Tests\TestCase;

class DashboardAnalyzeTest extends TestCase
{
    /**
     * The dashboard analyze POST route URI.
     */
    private const ANALYZE_URI = '/domo/analyze';

    /**
     * Register the package web routes for the test application.
     *
     * The service provider skips route loading while running in console
     * (i.e. during PHPUnit), so we load the very same route file here to
     * exercise the real route definitions.
     *
     * @param  Router  $router
     */
    protected function defineRoutes($router): void
    {
        require __DIR__.'/../../src/Http/Routes/web.php';
    }

    /**
     * Define environment setup.
     *
     * The dashboard routes run through the `web` middleware group, which
     * needs an application encryption key for the session/cookie pipeline.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    /**
     * Bind a fake schema analyzer so the controller can collect a payload
     * without touching a real database connection.
     *
     * @param  array<int, string>  $tables
     * @param  array<int, string>  $models
     */
    private function fakeAnalyzer(array $tables = ['users'], array $models = []): void
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);
        $analyzer->method('getTables')->willReturn($tables);
        $analyzer->method('getModels')->willReturn($models);
        $analyzer->method('getTableSchema')->willReturn(['columns' => ['id', 'name']]);
        $analyzer->method('analyzeModelRelationships')->willReturn([]);

        $this->app->instance(SchemaAnalyzerInterface::class, $analyzer);
    }

    public function test_analyze_with_valid_schema_type_returns_success_json(): void
    {
        $this->fakeAnalyzer(tables: ['users']);

        $ai = $this->createMock(AiDriverInterface::class);
        $ai->expects($this->once())
            ->method('analyzeSchema')
            ->willReturn(['summary' => 'Looks good']);
        $this->app->instance(AiDriverInterface::class, $ai);

        $response = $this->postJson(self::ANALYZE_URI, [
            'type' => 'schema',
            'target' => 'users',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'type' => 'schema',
            'target' => 'users',
            'result' => ['summary' => 'Looks good'],
        ]);
    }

    public function test_analyze_with_relationships_type_calls_suggest_relationships(): void
    {
        $this->fakeAnalyzer(tables: ['users'], models: ['App\\Models\\User']);

        $ai = $this->createMock(AiDriverInterface::class);
        $ai->expects($this->once())
            ->method('suggestRelationships')
            ->willReturn(['App\\Models\\User' => ['hasMany' => 'Post']]);
        $ai->expects($this->never())->method('analyzeSchema');
        $this->app->instance(AiDriverInterface::class, $ai);

        $response = $this->postJson(self::ANALYZE_URI, [
            'type' => 'relationships',
        ]);

        $response->assertOk();
        $response->assertJsonPath('success', true);
        $response->assertJsonPath('type', 'relationships');
        $response->assertJsonPath('result.App\\Models\\User.hasMany', 'Post');
    }

    public function test_analyze_with_invalid_type_returns_validation_error_422(): void
    {
        $this->fakeAnalyzer();

        $ai = $this->createMock(AiDriverInterface::class);
        $ai->expects($this->never())->method('analyzeSchema');
        $ai->expects($this->never())->method('suggestRelationships');
        $this->app->instance(AiDriverInterface::class, $ai);

        $response = $this->postJson(self::ANALYZE_URI, [
            'type' => 'not-a-valid-type',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');
    }

    public function test_analyze_without_type_returns_validation_error_422(): void
    {
        $this->fakeAnalyzer();

        $ai = $this->createMock(AiDriverInterface::class);
        $this->app->instance(AiDriverInterface::class, $ai);

        $response = $this->postJson(self::ANALYZE_URI, []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('type');
    }

    public function test_analyze_when_driver_throws_returns_failure_json_422(): void
    {
        $this->fakeAnalyzer(tables: ['users']);

        $ai = $this->createMock(AiDriverInterface::class);
        $ai->method('analyzeSchema')
            ->willThrowException(new AiDriverException('Missing API key.'));
        $this->app->instance(AiDriverInterface::class, $ai);

        $response = $this->postJson(self::ANALYZE_URI, [
            'type' => 'schema',
            'target' => 'users',
        ]);

        $response->assertStatus(422);
        $response->assertExactJson([
            'success' => false,
            'message' => 'Missing API key.',
        ]);
    }
}
