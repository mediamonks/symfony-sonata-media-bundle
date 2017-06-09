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
}
