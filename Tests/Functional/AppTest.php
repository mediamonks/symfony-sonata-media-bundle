<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

class AppTest extends BaseFunctionTest
{
    public function testReady()
    {
        $client = static::createClient();
        $client->request('GET', '/ready');

        $this->assertEquals('MediaMonks Functional Test App Ready', $client->getResponse()->getContent());
    }

    public function testAdminDashboard()
    {
        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/admin/dashboard');

        $this->assertContains('Dashboard', $client->getResponse()->getContent());
    }

    public function testAdminMediaListEmpty()
    {
        $this->loadFixtures([]);

        $client = $this->getAuthenticatedClient();
        $client->request('GET', '/mediamonks/sonatamedia/media/list');

        $this->assertContains('No result', $client->getResponse()->getContent());
    }
}
