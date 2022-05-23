<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Exception;
use MediaMonks\SonataMediaBundle\Handler\SignatureParameterHandler;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use Mockery as m;
use Symfony\Component\DomCrawler\Crawler;

class FileProviderTest extends AdminTestAbstract
{
    public function testFile()
    {
        $crawler = $this->uploadFile();

        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, ['title' => 'text']);

        $this->client->request('GET', self::BASE_PATH . 'list');
        $this->assertStringContainsString('text', $this->client->getResponse()->getContent());

        $this->assertNumberOfFilesInPath(2, $this->getMediaPathPrivate());
    }

    public function testFileDownload()
    {
        $crawler = $this->uploadFile();

        $link = $crawler->selectLink('Download original')->link();

        ob_start();
        $this->client->click($link);
        $fileContents = $content = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('foobar', $fileContents);
        $this->assertTrue($this->client->getResponse()->isSuccessful(), 'response status is 2xx');

        $media = m::mock(MediaInterface::class);
        $media->shouldReceive('getId')->andReturn(1);

        $parameterBag = new DownloadParameterBag();
        $signature = new SignatureParameterHandler(self::$kernel->getContainer()->getParameter('secret'));
        $parameters = $signature->getRouteParameters($media, $parameterBag);

        ob_start();
        $this->client->request(
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

        $this->assertStringContainsString('An error has occurred during the creation of item', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('not allowed to upload a file with extension', $this->client->getResponse()->getContent());
    }

    public function testEmptyFileUpload()
    {
        $provider = 'file';

        $crawler = $this->client->request('GET', self::BASE_PATH . 'create?provider=' . $provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->client->submit($form);

        $this->assertStringContainsString('This value should not be blank', $this->client->getResponse()->getContent());
        $this->assertStringContainsString('This value should not be blank', $this->client->getResponse()->getContent());
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

        $crawler = $this->client->request('GET', self::BASE_PATH . 'create?provider=' . $provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->setFormBinaryContent($form, $this->getFixturesPath() . $fileName);

        return $this->client->submit($form);
    }
}
