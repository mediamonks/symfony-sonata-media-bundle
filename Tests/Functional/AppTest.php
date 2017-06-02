<?php

namespace MediaMonks\RestApiBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase;

class AppTest extends WebTestCase
{
    private function getAuthenticatedClient()
    {
        return $this->makeClient(true);
    }

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

    public function testYoutubeAdd()
    {
        $this->loadFixtures([]);

        $client = $this->getAuthenticatedClient();
        $client->followRedirects();

        $crawler = $client->request('GET', '/mediamonks/sonatamedia/media/create?provider=youtube');

        $this->assertContains('YouTube ID or URL', $client->getResponse()->getContent());

        $form = $crawler->selectButton('Create')->form();

        foreach ($form->getValues() as $k => $v) {
            if (strpos($k, '[provider]') !== false) {
                $this->assertEquals('youtube', $v);
            }
            if (strpos($k, '[providerReference]') !== false) {
                $form->setValues([
                    $k => 'https://www.youtube.com/watch?v=uN2nqdRLhQ8'
                ]);
            }
        }

        $crawler = $client->submit($form);
        $form = $crawler->selectButton('Update')->form();
        $formValues = $this->getSonataFormValues($form);

        $this->assertEquals('youtube', $formValues['provider']);
        $this->assertEquals('uN2nqdRLhQ8', $formValues['providerReference']);
        $this->assertEquals('MediaMonks Mixtape Vol  II', $formValues['title']);
        $this->assertEquals('MediaMonks', $formValues['authorName']);

        $client->request('GET', '/mediamonks/sonatamedia/media/list');
        $this->assertContains('MediaMonks Mixtape Vol  II', $client->getResponse()->getContent());
    }

    private function getSonataFormValues($form)
    {
        $values = [];
        foreach ($form->getValues() as $k => $v) {
            if (preg_match('~\[(.*)\]~', $k, $matches)) {
                $values[$matches[1]] = $v;
            }
        }

        return $values;
    }
}
