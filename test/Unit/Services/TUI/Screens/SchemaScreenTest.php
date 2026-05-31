<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit\Services\TUI\Screens;

use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\TUI\Actions\Navigate;
use Jemgdevp\Domo\Services\TUI\Actions\Stay;
use Jemgdevp\Domo\Services\TUI\Screens\SchemaScreen;
use Jemgdevp\Domo\Tests\TestCase;

class SchemaScreenTest extends TestCase
{
    public function test_escape_navigates_back_to_home(): void
    {
        $analyzer = $this->mockAnalyzer();
        $screen = new SchemaScreen($analyzer);

        $action = $screen->handle((object) ['code' => 'esc']);

        $this->assertInstanceOf(Navigate::class, $action);
    }

    public function test_down_key_keeps_user_in_screen(): void
    {
        $analyzer = $this->mockAnalyzer();
        $screen = new SchemaScreen($analyzer);

        $action = $screen->handle((object) ['code' => 'down']);

        $this->assertInstanceOf(Stay::class, $action);
    }

    protected function mockAnalyzer(): SchemaAnalyzerInterface
    {
        $analyzer = $this->createMock(SchemaAnalyzerInterface::class);
        $analyzer->method('getTables')->willReturn(['users']);
        $analyzer->method('getTableSchema')->willReturn([
            'columns' => [
                ['name' => 'id', 'type' => 'bigint', 'nullable' => 'no', 'default' => null, 'key' => 'PRI'],
            ],
        ]);

        return $analyzer;
    }
}
