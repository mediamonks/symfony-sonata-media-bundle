<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\DependencyInjection;

use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use MediaMonks\SonataMediaBundle\DependencyInjection\MediaMonksSonataMediaExtension;

class MediaMonksSonataMediaExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return [
            new MediaMonksSonataMediaExtension(),
        ];
    }

    public function testMissingFilesystem()
    {
        $this->expectException(\Exception::class);
        $this->load();
    }
}
