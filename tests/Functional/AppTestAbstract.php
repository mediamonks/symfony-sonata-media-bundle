<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class AppTestAbstract extends AbstractBaseFunctionTest
{
    public function testReady()
    {
        $client = static::createClient();
        $client->request('GET', '/ready');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('MediaMonks Functional Test App Ready', $response->getContent());
    }

    public function testAdminDashboard()
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/admin/dashboard');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('Dashboard', $response->getContent());
    }

    public function testAdminMediaListEmpty()
    {
        $this->loadFixtures();

        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/mediamonks/sonatamedia/media/list');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('No result', $response->getContent());
    }
}
