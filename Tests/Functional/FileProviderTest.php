<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Symfony\Component\DomCrawler\Crawler;

class FileProviderTest extends AdminTestAbstract
{
    public function testFile()
    {
        $crawler = $this->uploadFile();

        $form = $crawler->selectButton('Update')->form();

        $this->assertSonataFormValues($form, ['title' => 'text']);

        $this->client->request('GET', self::BASE_PATH.'list');
        $this->assertContains('text', $this->client->getResponse()->getContent());

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
    }

    public function testInvalidFileUpload()
    {
        $this->uploadFile('text.exe');

        $this->assertContains('An error has occurred during the creation of item', $this->client->getResponse()->getContent());
        $this->assertContains('not allowed to upload a file with extension', $this->client->getResponse()->getContent());
    }

    /**
     * @param string $fileName
     * @return Crawler
     */
    private function uploadFile($fileName = 'text.txt')
    {
        $provider = 'file';

        $crawler = $this->client->request('GET', self::BASE_PATH.'create?provider='.$provider);

        $form = $crawler->selectButton('Create')->form();

        $this->assertSonataFormValues(
            $form,
            [
                'provider' => $provider,
            ]
        );

        $this->setFormBinaryContent($form, $this->getFixturesPath().$fileName);

        $crawler = $this->client->submit($form);

        return $crawler;
    }
}
