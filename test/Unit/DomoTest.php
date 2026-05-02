<?php

declare(strict_types=1);

namespace Jemgdevp\Domo\Tests\Unit;

use Jemgdevp\Domo\Tests\TestCase;

class DomoTest extends TestCase
{
    public function test_domo_can_be_instantiated()
    {
        $this->assertInstanceOf(Domo::class, new Domo());
    }
}
