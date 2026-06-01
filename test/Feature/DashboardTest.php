<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Feature;

use Illuminate\Foundation\Application;
use Illuminate\Routing\Router;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Tests\TestCase;

/**
 * Renders every dashboard GET page through the real controller + Blade stack
 * (layout + tokens/sidebar/topbar/command-palette/toasts/shell-script partials),
 * guaranteeing the redesigned views compile and resolve without runtime errors.
 */
class DashboardTest extends TestCase
{
    /**
     * Register the package web routes for the test application.
     *
     * @param  Router  $router
     */
    protected function defineRoutes($router): void
    {
        require __DIR__.'/../../src/Http/Routes/web.php';
    }

    /**
     * The dashboard runs through the `web` middleware group, which needs an
     * application key for the session/cookie pipeline.
     *
     * @param  Application  $app
     */
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    /**
     * Bind a fake schema analyzer returning realistic, driver-shaped data so
     * the views exercise their defensive normalization (table rows as objects,
     * columns as associative arrays) without touching a real database.
     */
    private function fakeAnalyzer(): void
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);

        // Raw driver rows are stdClass objects whose column name varies per
        // driver — exactly what the in-view normalization must survive.
        $analyzer->method('getTables')->willReturn([
            (object) ['Tables_in_testing' => 'users'],
            (object) ['Tables_in_testing' => 'posts'],
        ]);

        $analyzer->method('getModels')->willReturn([
            'App\\Models\\User',
            'App\\Models\\Post',
        ]);

        $analyzer->method('getTableSchema')->willReturn([
            'columns' => [
                ['Field' => 'id', 'Type' => 'bigint', 'Null' => 'NO', 'Key' => 'PRI', 'Default' => null],
                ['Field' => 'name', 'Type' => 'varchar(255)', 'Null' => 'YES', 'Key' => '', 'Default' => null],
            ],
        ]);

        $analyzer->method('analyzeModelRelationships')->willReturn([
            'posts' => 'App\\Models\\Post',
        ]);

        $this->app->instance(SchemaAnalyzerInterface::class, $analyzer);
    }

    public function test_dashboard_index_page_renders(): void
    {
        $this->fakeAnalyzer();

        $response = $this->get('/domo');

        $response->assertOk();
        $response->assertSee('Domo', false);          // shared shell (topbar)
        $response->assertSee('Total Tables');         // stat card
        $response->assertSee('users');                // normalized object table row
        $response->assertSee('User');                 // class_basename of a model
    }

    public function test_dashboard_schema_page_renders(): void
    {
        $this->fakeAnalyzer();

        $response = $this->get('/domo/schema');

        $response->assertOk();
        $response->assertSee('users');                // table name (Js::from payload survives)
        $response->assertSee('id');                   // column field
        $response->assertSee('bigint');               // column type
    }

    public function test_dashboard_models_page_renders(): void
    {
        $this->fakeAnalyzer();

        $response = $this->get('/domo/models');

        $response->assertOk();
        $response->assertSee('User');                 // class_basename
        $response->assertSee('posts');                // relationship method
    }

    public function test_dashboard_analyze_page_renders(): void
    {
        $this->fakeAnalyzer();

        $response = $this->get('/domo/analyze');

        $response->assertOk();
        $response->assertSee('AI Analysis');          // page title
        $response->assertSee('Run analysis');         // submit affordance
        $response->assertSee('Provider / variant');   // provider selector
        $response->assertSee('opencode');             // configured provider option
    }
}
