<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

class InvalidProviderTest extends AdminTestAbstract
{
    public function testInvalidProvider()
    {
        $this->browser->request('GET', self::BASE_PATH . 'create?provider=foo');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->browser->getResponse()->getStatusCode());
    }

    public function testNoProviderLoadsProviderSelection()
    {
        $this->browser->request('GET', self::BASE_PATH . 'create');
        $this->assertEquals(Response::HTTP_OK, $this->browser->getResponse()->getStatusCode());
        $this->assertStringContainsString('Select provider', $this->browser->getResponse()->getContent());
    }
}
