<?php

namespace MediaMonks\SonataMediaBundle\Tests\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MediaMonks\SonataMediaBundle\DependencyInjection\MediaMonksSonataMediaExtension;

class MediaMonksSonataMediaExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions()
    {
        return [
            new MediaMonksSonataMediaExtension(),
        ];
    }

    public function testDefaultMediaEntity()
    {
        $this->load($this->getDefaultContainerParameters());
        $this->assertContainerBuilderHasParameter('mediamonks.sonata_media.entity.class', 'AppBundle\Entity\Media');
    }

    public function testCustomMediaEntity()
    {
        $this->load(array_merge([
            'model_class' => 'CustomAppBundle\Entity\Media'
        ], $this->getDefaultContainerParameters()));
        $this->assertContainerBuilderHasParameter('mediamonks.sonata_media.entity.class', 'CustomAppBundle\Entity\Media');
    }

    public function testMissingFilesystem()
    {
        $this->setExpectedException(\Exception::class);
        $this->load();
    }

    /**
     * @return array
     */
    private function getDefaultContainerParameters()
    {
        return [
            'filesystem_private' => 'foo',
            'filesystem_public' => 'foo',
        ];
    }
}
