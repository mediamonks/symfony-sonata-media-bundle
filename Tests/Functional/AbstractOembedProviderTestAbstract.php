<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

abstract class AbstractOembedProviderTestAbstract extends AdminTestAbstract
{
    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerFlow($provider, $providerReference, array $expectedValues)
    {
        \VCR\VCR::insertCassette($provider);
        $this->providerAdd($provider, $providerReference, $expectedValues);
        $this->providerUpdate($provider, $providerReference, $expectedValues);
        $this->verifyMediaImageIsGenerated();
        \VCR\VCR::eject();
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerAdd($provider, $providerReference, array $expectedValues)
    {
        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->updateSonataFormValues(
            $form,
            [
                'providerReference' => $providerReference,
            ]
        );

        $crawler = $this->client->submit($form);

        $form = $crawler->selectButton('Update')->form();

        $this->assertContains('has been successfully created', $this->client->getResponse()->getContent());
        $this->assertSonataFormValues($form, $expectedValues);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains($expectedValues['title'], $this->client->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(1, $this->getMediaPathPrivate());
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
            'title'       => 'Updated Title',
            'description' => 'Updated Description',
            'author'      => 'Updated Author',
            'copyright'   => 'Updated Copyright',
        ];

        $form = $crawler->selectButton('Update')->form();
        $this->updateSonataFormValues($form, $update);
        $this->client->submit($form);

        $this->assertSonataFormValues(
            $form,
            array_merge(
                $update,
                [
                    'provider'          => $provider,
                    'providerReference' => $expectedValues['providerReference'],
                ]
            )
        );

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains($update['title'], $this->client->getResponse()->getContent());
    }
}
