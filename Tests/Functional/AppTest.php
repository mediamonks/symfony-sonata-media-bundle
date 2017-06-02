<?php

namespace MediaMonks\RestApiBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AppTest extends WebTestCase
{
    private function getAuthenticatedClient()
    {
        return static::createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'admin',
        ));
    }

    public function testReady()
    {
        $client = static::createClient();
        $client->request('GET', '/ready');

        $this->assertEquals('MediaMonks Functional Test App Ready', $client->getResponse()->getContent());
    }

    public function testAdminResponse()
    {
        $client = static::createClient();
        $client->request('GET', '/admin/');

        $this->assertContains('Dashboard', $client->getResponse()->getContent());
    }

    public function testAdminMediaList()
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/mediamonks/sonatamedia/media/list');

        echo $client->getResponse()->getContent();
    }
}
