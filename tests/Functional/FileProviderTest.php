<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Exception;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Mockery as m;
use Symfony\Component\DomCrawler\Crawler;

class FileProviderTest extends AdminTestAbstract
{
    public function testFile()
    {
        $crawler = $this->uploadFile();

        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, ['title' => 'text']);

        $this->browser->request('GET', self::BASE_PATH . 'list');
        $this->assertStringContainsString('text', $this->browser->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(2, $this->getMediaPathPrivate());
    }

    public function testFileDownload()
    {
        $crawler = $this->uploadFile();

        $link = $crawler->selectLink('Download original')->link();

        ob_start();
        $this->browser->click($link);
        $fileContents = $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('foobar', $fileContents);
        $this->assertTrue($this->browser->getResponse()->isSuccessful(), 'response status is 2xx');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);

        $parameterBag = new MediaParameterBag();
        $signature = new SignatureParameterHandler(self::$kernel->getContainer()->getParameter('secret'));
        $parameters = $signature->getRouteParameters($media, $parameterBag);

        ob_start();
        $this->browser->request(
            'GET',
            sprintf(
                '/media/download/%d?s=%s',
                $media->getId(),
                $parameters['s']
            )
        );
        $fileContents = $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('foobar', $fileContents);
    }

    public function testInvalidFileUpload()
    {
        $this->uploadFile('text.exe');

        $this->assertStringContainsString('An error has occurred during the creation of item', $this->browser->getResponse()->getContent());
        $this->assertStringContainsString('not allowed to upload a file with extension', $this->browser->getResponse()->getContent());
    }

    public function testEmptyFileUpload()
    {
        $provider = 'file';

        $crawler = $this->browser->request('GET', self::BASE_PATH . 'create?provider=' . $provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->browser->submit($form);

        $this->assertStringContainsString('This value should not be blank', $this->browser->getResponse()->getContent());
        $this->assertStringContainsString('This value should not be blank', $this->browser->getResponse()->getContent());
    }

    /**
     * @param string $fileName
     *
     * @return Crawler
     * @throws Exception
     */
    private function uploadFile(string $fileName = 'text.txt'): Crawler
    {
        $provider = 'file';

        $crawler = $this->browser->request('GET', self::BASE_PATH . 'create?provider=' . $provider);
        $selectButton = $crawler->selectButton('Create');
        if ($selectButton->count() === 0) {
            $this->output($crawler);
        }
        $form = $selectButton->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->setFormBinaryContent($form, $this->getFixturesPath() . $fileName);

        return $this->browser->submit($form);
    }
}
