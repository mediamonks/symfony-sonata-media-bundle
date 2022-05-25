<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit;

use Mockery as m;

trait MockeryTrait
{
    protected function setUp(): void
    {
        parent::setUp();

        m::resetContainer();
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        m::close();
    }
}