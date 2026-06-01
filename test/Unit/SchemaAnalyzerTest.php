<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface;
use Jemgdevp\Domo\Services\Schema\Analyzer;
use Jemgdevp\Domo\Tests\TestCase;

class SchemaAnalyzerTest extends TestCase
{
    public function test_analyzer_can_be_instantiated(): void
    {
        $analyzer = new Analyzer;
        $this->assertInstanceOf(Analyzer::class, $analyzer);
    }

    public function test_analyzer_implements_interface(): void
    {
        $analyzer = new Analyzer;
        $this->assertInstanceOf(SchemaAnalyzerInterface::class, $analyzer);
    }

    /**
     * Exercises the real introspection path (getQueryGrammar()->wrapTable()).
     * Regression guard for "Call to a member function wrapTable() on null",
     * which getSchemaGrammar() triggered on a fresh connection.
     */
    public function test_get_table_schema_returns_normalized_columns_for_real_table(): void
    {
        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->string('title');
            $table->string('subtitle')->nullable();
        });

        $schema = (new Analyzer)->getTableSchema('posts');

        $this->assertArrayHasKey('columns', $schema);
        $this->assertCount(3, $schema['columns']);

        $byField = [];
        foreach ($schema['columns'] as $column) {
            $byField[$column['Field']] = $column;
        }

        $this->assertSame('PRI', $byField['id']['Key']);     // primary key detected
        $this->assertSame('NO', $byField['title']['Null']);  // NOT NULL mapping
        $this->assertSame('YES', $byField['subtitle']['Null']); // nullable mapping
    }

    public function test_get_tables_lists_a_created_table(): void
    {
        Schema::create('widgets', function (Blueprint $table): void {
            $table->id();
        });

        $names = array_map(
            static fn ($row) => is_object($row) ? ($row->name ?? null) : ($row['name'] ?? null),
            (new Analyzer)->getTables()
        );

        $this->assertContains('widgets', $names);
    }
}
