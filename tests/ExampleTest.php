<?php

namespace Adeliom\WP\Tests;

use Orchestra\Testbench\TestCase;

class ExampleTest extends TestCase
{

    protected function getPackageProviders($app)
    {
        return [];
    }

    /** @test */
    public function true_is_true()
    {
        $this->assertTrue(true);
    }
}
