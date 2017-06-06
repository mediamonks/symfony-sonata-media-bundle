<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Bundle\FrameworkBundle\Client;

abstract class AbstractBaseFunctionTest extends WebTestCase
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            $this->markTestSkipped('Functional tests only run on PHP 5.6+');
        }

        parent::setUp();

        $this->emptyFolder($this->getMediaPathPublic());
        $this->emptyFolder($this->getMediaPathPrivate());
    }

    /**
     * @return string
     */
    protected function getMediaPathPublic()
    {
        return __DIR__.'/web/media/';
    }

    /**
     * @return string
     */
    protected function getMediaPathPrivate()
    {
        return __DIR__.'/var/media/';
    }

    /**
     * @return string
     */
    protected function getFixturesPath()
    {
        return __DIR__.'/var/fixtures/';
    }

    /**
     * @param int $amount
     * @param string $path
     */
    protected function assertNumberOfFilesInPath($amount, $path)
    {
        $fi = new \FilesystemIterator($path, \FilesystemIterator::SKIP_DOTS);
        $this->assertEquals($amount, iterator_count($fi));
    }

    /**
     * @param string $path
     * @return bool
     */
    protected function emptyFolder($path)
    {
        $di = new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir($file) : unlink($file);
        }

        return true;
    }

    /**
     * @return Client
     */
    protected function getAuthenticatedClient()
    {
        return $this->makeClient(true);
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
     * @return array
     */
    protected function getSonataFormValues(Form $form)
    {
        $values = [];
        foreach ($form->getValues() as $k => $v) {
            if (preg_match('~\[(.*)\]~', $k, $matches)) {
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

    protected function getSonataFormBaseKey(Form $form)
    {
        foreach ($form->getValues() as $k => $v) {
            if (preg_match('~(.*)\[(.*)\]~', $k, $matches)) {
                return $matches[1];
            }
        }
    }

    /**
     * @param Form $form
     * @param string $file
     */
    protected function setFormBinaryContent(Form $form, $file)
    {
        $baseKey = $this->getSonataFormBaseKey($form);

        $form[sprintf('%s[binaryContent]', $baseKey)]->upload($file);
    }
}
