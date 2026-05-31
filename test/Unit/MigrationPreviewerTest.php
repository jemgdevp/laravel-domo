<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Services\Migration\MigrationPreviewer;
use Jemgdevp\Domo\Tests\TestCase;

class MigrationPreviewerTest extends TestCase
{
    /**
     * A representative Schema::create migration body.
     */
    private function createMigration(): string
    {
        return <<<'PHP'
        <?php

        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        return new class extends Migration
        {
            public function up(): void
            {
                Schema::create('posts', function (Blueprint $table) {
                    $table->id();
                    $table->string('title');
                    $table->text('body');
                    $table->unsignedBigInteger('user_id');
                    $table->unique('title');
                    $table->index('user_id');
                });
            }
        };
        PHP;
    }

    public function test_preview_create_migration_returns_non_empty_operations_and_sql(): void
    {
        $previewer = new MigrationPreviewer;

        $result = $previewer->preview($this->createMigration());

        $this->assertArrayHasKey('operations', $result);
        $this->assertArrayHasKey('sql', $result);
        $this->assertNotEmpty($result['operations']);
        $this->assertNotEmpty($result['sql']);
    }

    public function test_preview_create_migration_operations_describe_table_and_columns(): void
    {
        $previewer = new MigrationPreviewer;

        $result = $previewer->preview($this->createMigration());

        $this->assertContains('create table posts', $result['operations']);
        $this->assertContains('add column id BIGINT UNSIGNED AUTO_INCREMENT', $result['operations']);
        $this->assertContains('add column title VARCHAR(255)', $result['operations']);
        $this->assertContains('add column body TEXT', $result['operations']);
        $this->assertContains('add column user_id BIGINT UNSIGNED', $result['operations']);
    }

    public function test_preview_create_migration_sql_contains_create_table_statement(): void
    {
        $previewer = new MigrationPreviewer;

        $result = $previewer->preview($this->createMigration());

        $sql = implode("\n", $result['sql']);

        $this->assertStringContainsString('CREATE TABLE `posts`', $sql);
        $this->assertStringContainsString('`title` VARCHAR(255)', $sql);
        $this->assertStringContainsString('`user_id` BIGINT UNSIGNED', $sql);
    }

    public function test_get_statistics_counts_columns_indexes_and_tables_for_create_migration(): void
    {
        $previewer = new MigrationPreviewer;

        $statistics = $previewer->getStatistics($this->createMigration());

        $this->assertSame(1, $statistics['tables_affected']);
        $this->assertSame(4, $statistics['columns_added']);
        $this->assertSame(0, $statistics['columns_dropped']);
        $this->assertSame(2, $statistics['indexes_added']);
    }

    public function test_get_statistics_counts_multiple_tables(): void
    {
        $migration = <<<'PHP'
        <?php

        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        Schema::create('authors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignId('author_id');
        });
        PHP;

        $previewer = new MigrationPreviewer;

        $statistics = $previewer->getStatistics($migration);

        $this->assertSame(2, $statistics['tables_affected']);
        $this->assertSame(5, $statistics['columns_added']);
    }

    public function test_get_statistics_counts_dropped_columns_for_alter_migration(): void
    {
        $migration = <<<'PHP'
        <?php

        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;

        Schema::table('users', function (Blueprint $table) {
            $table->string('nickname');
            $table->dropColumn('legacy_field');
        });
        PHP;

        $previewer = new MigrationPreviewer;

        $statistics = $previewer->getStatistics($migration);

        $this->assertSame(1, $statistics['tables_affected']);
        $this->assertSame(1, $statistics['columns_added']);
        $this->assertSame(1, $statistics['columns_dropped']);
    }

    public function test_preview_empty_string_returns_empty_structure_without_exception(): void
    {
        $previewer = new MigrationPreviewer;

        $result = $previewer->preview('');

        $this->assertSame([], $result['operations']);
        $this->assertSame([], $result['sql']);
    }

    public function test_preview_whitespace_only_returns_empty_structure(): void
    {
        $previewer = new MigrationPreviewer;

        $result = $previewer->preview("   \n\t  ");

        $this->assertSame([], $result['operations']);
        $this->assertSame([], $result['sql']);
    }

    public function test_get_statistics_empty_string_returns_zeroed_statistics_without_exception(): void
    {
        $previewer = new MigrationPreviewer;

        $statistics = $previewer->getStatistics('');

        $this->assertSame(0, $statistics['tables_affected']);
        $this->assertSame(0, $statistics['columns_added']);
        $this->assertSame(0, $statistics['columns_dropped']);
        $this->assertSame(0, $statistics['indexes_added']);
    }
}
