<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Services\Schema\Analyzer;
use Jemgdevp\Domo\Tests\TestCase;

class SchemaAnalyzerTest extends TestCase
{
    public function test_analyzer_can_be_instantiated(): void
    {
        $analyzer = new Analyzer();
        $this->assertInstanceOf(Analyzer::class, $analyzer);
    }

    public function test_analyzer_implements_interface(): void
    {
        $analyzer = new Analyzer();
        $this->assertInstanceOf(\Jemgdevp\Domo\Contracts\SchemaAnalyzerInterface::class, $analyzer);
    }
}
