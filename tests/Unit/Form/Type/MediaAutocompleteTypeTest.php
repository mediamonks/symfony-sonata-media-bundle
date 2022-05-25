<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Form\Type\MediaAutocompleteType;
use Mockery as m;
use Sonata\AdminBundle\Model\ModelManagerInterface;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Twig\Environment;

class MediaAutocompleteTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $modelManager = m::mock(ModelManagerInterface::class);
        $modelManager->shouldReceive('getModelCollectionInstance')->andReturn(new ArrayCollection());

        $twig = m::mock(Environment::class);
        $type = new MediaAutocompleteType($modelManager, $twig, Media::class);

        return [
            new PreloadedExtension([$type], []),
        ];
    }

    public function testSubmitValidData()
    {
        $form = $this->factory->create(MediaAutocompleteType::class);
        $form->submit([]);
        $this->assertTrue($form->isSynchronized());
    }
}
