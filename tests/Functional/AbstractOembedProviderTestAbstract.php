<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use VCR\VCR;

abstract class AbstractOembedProviderTestAbstract extends AdminTestAbstract
{
    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerFlow($provider, $providerReference, array $expectedValues)
    {
        VCR::insertCassette($provider);
        $this->providerAdd($provider, $providerReference, $expectedValues);
        $this->providerUpdate($provider, $providerReference, $expectedValues);
        $this->verifyMediaImageIsGenerated();
        VCR::eject();
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerAdd(string $provider, string $providerReference, array $expectedValues)
    {
        $crawler = $this->browser->request('GET', self::BASE_PATH . 'create?provider=' . $provider);
        $createButton = $crawler->selectButton('Create');
        if ($createButton->count() === 0) {
            $this->output($crawler);
        }
        $form = $createButton->form();
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

        $crawler = $this->browser->submit($form);
        $updateButton = $crawler->selectButton('Update');
        if ($updateButton->count() === 0) {
            $this->output($crawler);
        }
        $form = $updateButton->form();
        $this->assertStringContainsString('has been successfully created', $this->browser->getResponse()->getContent());
        $this->assertSonataFormValues($form, $expectedValues);

        $this->browser->request('GET', self::BASE_PATH . 'list');
        $this->assertStringContainsString($expectedValues['title'], $this->browser->getResponse()->getContent());

        $this->verifyMediaImageIsGenerated();

        $this->assertEquals(
            3,
            $crawler->filter('img')->count()
        );

        $this->assertEquals(
            1,
            $crawler->filter('iframe')->count()
        );
    }

    /**
     * @param string $provider
     * @param string $providerReference
     * @param array $expectedValues
     */
    protected function providerUpdate($provider, $providerReference, array $expectedValues)
    {
        $crawler = $this->browser->request('GET', self::BASE_PATH . '1/edit');

        $this->assertStringContainsString($expectedValues['title'], $this->browser->getResponse()->getContent());

        $update = [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'author' => 'Updated Author',
            'copyright' => 'Updated Copyright',
            'focalPoint' => '75-25',
        ];

        $form = $crawler->selectButton('Update')->form();
        $this->updateSonataFormValues($form, $update);
        $this->browser->submit($form);

        $this->assertSonataFormValues(
            $form,
            array_merge(
                $update,
                [
                    'provider' => $provider,
                    'providerReference' => $expectedValues['providerReference'],
                ]
            )
        );

        $this->browser->request('GET', self::BASE_PATH . 'list');
        $this->assertStringContainsString($update['title'], $this->browser->getResponse()->getContent());
    }
}
