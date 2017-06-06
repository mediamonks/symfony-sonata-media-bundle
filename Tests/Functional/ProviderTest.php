<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Client;

abstract class ProviderTest extends BaseFunctionTest
{
    const BASE_PATH = '/mediamonks/sonatamedia/media/';

    /**
     * @var Client
     */
    private $client;

    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            $this->markTestSkipped('Functional tests only run on PHP 5.6+');
        }

        parent::setUp();

        $this->client = $this->getAuthenticatedClient();
        $this->client->followRedirects();
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerFlow($provider, $providerReference, array $expectedValues)
    {
        $this->providerAdd($provider, $providerReference, $expectedValues);
        $this->providerUpdate($provider, $providerReference, $expectedValues);
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerAdd($provider, $providerReference, array $expectedValues)
    {
        $this->loadFixtures([]);

        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues($form, [
            'provider' => $provider,
        ]);

        $this->updateFormValues($form, [
            'providerReference' => $providerReference
        ]);

        $crawler = $this->client->submit($form);
        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, $expectedValues);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains($expectedValues['title'], $this->client->getResponse()->getContent());
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerUpdate($provider, $providerReference, array $expectedValues)
    {
        $crawler = $this->client->request('GET', self::BASE_PATH.'1/edit');

        $this->assertContains($expectedValues['title'], $this->client->getResponse()->getContent());

        $update = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'author' => 'Updated Author',
            'copyright' => 'Updated Copyright'
        ];

        $form = $crawler->selectButton('Update')->form();
        $this->updateFormValues($form, $update);
        $this->client->submit($form);

        $this->assertSonataFormValues($form, array_merge($update, [
            'provider' => $provider,
            'providerReference' => $expectedValues['providerReference'],
        ]));

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains($update['title'], $this->client->getResponse()->getContent());
    }
}
