<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Symfony\Component\HttpFoundation\Response;

class InvalidProviderTest extends AdminTestAbstract
{
    public function testInvalidProvider()
    {
        $this->client->request('GET', self::BASE_PATH.'create?provider=foo');
        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $this->client->getResponse()->getStatusCode());
    }

    public function testNoProviderLoadsProviderSelection()
    {
        $this->client->request('GET', self::BASE_PATH.'create');
        $this->assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Select provider', $this->client->getResponse()->getContent());
    }
}
