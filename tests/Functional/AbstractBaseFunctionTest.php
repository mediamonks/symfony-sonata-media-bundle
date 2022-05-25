<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Doctrine\Common\DataFixtures\ReferenceRepository;
use Exception;
use FilesystemIterator;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use MediaMonks\SonataMediaBundle\Tests\App\AppKernel;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\DomCrawler\Form;
use Symfony\Component\Finder\Finder;

abstract class AbstractBaseFunctionTest extends WebTestCase
{
    protected AbstractDatabaseTool $databaseTool;
    protected ReferenceRepository $referenceRepository;

    protected static function getKernelClass(): string
    {
        return AppKernel::class;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->emptyFolder($this->getMediaPathPublic());
        $this->emptyFolder($this->getMediaPathPrivate());

        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    protected function loadFixtures(): void
    {
        $this->referenceRepository = $this->databaseTool->loadFixtures()->getReferenceRepository();
    }

    /**
     * @return string
     */
    protected function getMediaPathPublic(): string
    {
        return __DIR__ . '/public/media/';
    }

    /**
     * @return string
     */
    protected function getMediaPathPrivate(): string
    {
        return __DIR__ . '/var/media/';
    }

    /**
     * @return string
     */
    protected function getFixturesPath(): string
    {
        return __DIR__ . '/var/fixtures/';
    }

    /**
     * @param int $amount
     * @param string $path
     */
    protected function assertNumberOfFilesInPath($amount, $path): void
    {
        $finder = new Finder();
        $finder->files()->in($path);
        $this->assertEquals($amount, $finder->count());
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    protected function emptyFolder($path): bool
    {
        if (file_exists($path)) {
            $di = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
            $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
            foreach ($ri as $file) {
                $file->isDir() ? rmdir($file) : unlink($file);
            }
        } else {
            @mkdir($path);
        }

        return true;
    }

    /**
     * @return KernelBrowser
     */
    protected function getAuthenticatedClient(): KernelBrowser
    {
        return $this->createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'admin',
        ]);
    }

    /**
     * @param Form $form
     * @param array $asserts
     */
    protected function assertSonataFormValues(Form $form, array $asserts)
    {
        foreach ($form->getValues() as $formKey => $formValue) {
            foreach ($asserts as $assertKey => $assertValue) {
                if (strpos($formKey, sprintf('[%s]', $assertKey)) !== false) {
                    $this->assertEquals($assertValue, $formValue);
                }
            }
        }
    }

    /**
     * @param Form $form
     *
     * @return array
     */
    protected function getSonataFormValues(Form $form)
    {
        $values = [];
        foreach ($form->getValues() as $k => $v) {
            if (preg_match('~\[(.*)]~', $k, $matches)) {
                $values[$matches[1]] = $v;
            }
        }

        return $values;
    }

    /**
     * @param Form $form
     * @param array $updates
     */
    protected function updateSonataFormValues(Form $form, array $updates)
    {
        foreach ($form->getValues() as $formKey => $formValue) {
            foreach ($updates as $updateKey => $updateValue) {
                if (strpos($formKey, sprintf('[%s]', $updateKey)) !== false) {
                    $form[$formKey] = $updateValue;
                }
            }
        }
    }

    /**
     * @param Form $form
     *
     * @return mixed
     */
    protected function getSonataFormBaseKey(Form $form)
    {
        foreach ($form->getValues() as $k => $v) {
            if (preg_match('~(.*)\[(.*)]~', $k, $matches)) {
                return $matches[1];
            }
        }

        throw new Exception('Could not find Sonata base key in form');
    }

    /**
     * @param Form $form
     * @param string $file
     *
     * @throws Exception
     */
    protected function setFormBinaryContent(Form $form, $file)
    {
        $baseKey = $this->getSonataFormBaseKey($form);
        $key = sprintf('%s[binaryContent]', $baseKey);
        if (!file_exists($file)) {
            throw new Exception('Upload file does not exist at: ' . $file);
        }
        $form[$key]->upload($file);
    }

    protected function output(Crawler $crawler): void
    {
        file_put_contents('output.html', $crawler->html());
    }
}
