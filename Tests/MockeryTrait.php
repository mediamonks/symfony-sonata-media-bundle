<?php

namespace MediaMonks\SonataMediaBundle\Tests;

use Mockery as m;

trait MockeryTrait
{
    protected function setUp()
    {
        parent::setUp();

        m::resetContainer();
    }

    protected function tearDown()
    {
        parent::tearDown();

        m::close();
    }
}