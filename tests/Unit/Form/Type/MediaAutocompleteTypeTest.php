<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Form\Type;

use Doctrine\Common\Collections\ArrayCollection;
use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Form\Type\MediaAutocompleteType;
use Sonata\DoctrineORMAdminBundle\Model\ModelManager;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Mockery as m;
use Twig\Environment;

class MediaAutocompleteTypeTest extends TypeTestCase
{
    protected function getExtensions()
    {
        $modelManager = m::mock(ModelManager::class);
        $modelManager->shouldReceive('getModelCollectionInstance')->andReturn(new ArrayCollection());

        $admin = m::mock(MediaAdmin::class);
        $admin->shouldReceive('getModelManager')->andReturn($modelManager);

        $twig = m::mock(Environment::class);

        $type = new MediaAutocompleteType($admin, $twig);

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
