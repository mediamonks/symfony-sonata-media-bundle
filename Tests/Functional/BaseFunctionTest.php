<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Form;
use Symfony\Bundle\FrameworkBundle\Client;

abstract class BaseFunctionTest extends WebTestCase
{
    protected function setUp()
    {
        if (version_compare(PHP_VERSION, '5.6.0', '<')) {
            $this->markTestSkipped('Functional tests only run on PHP 5.6+');
        }

        parent::setUp();
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
    protected function updateFormValues(Form $form, array $updates)
    {
        foreach ($form->getValues() as $formKey => $formValue) {
            foreach ($updates as $updateKey => $updateValue) {
                if (strpos($formKey, sprintf('[%s]', $updateKey)) !== false) {
                    $form->setValues([
                        $formKey => $updateValue
                    ]);
                }
            }
        }
    }
}
