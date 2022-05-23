<?php

namespace MediaMonks\SonataMediaBundle\Tests\Unit\Model;

use DateTimeImmutable;
use DateTimeInterface;
use MediaMonks\SonataMediaBundle\Provider\AbstractProvider;
use PHPUnit\Framework\TestCase;

class AbstractMediaTest extends TestCase
{
    public function testGettersSetters()
    {
        $media = new Media();

        $this->assertInstanceOf(DateTimeInterface::class, $media->getCreatedAt());
        $this->assertInstanceOf(DateTimeInterface::class, $media->getUpdatedAt());

        $media->setId(1);
        $this->assertEquals(1, $media->getId());
        $this->assertEquals('1', $media->getSlug());

        $media->setTitle('Title');
        $this->assertEquals('Title', $media->getTitle());

        $media->setDescription('Description');
        $this->assertEquals('Description', $media->getDescription());

        $media->setProvider('provider');
        $this->assertEquals('provider', $media->getProvider());

        $media->setType(AbstractProvider::TYPE_FILE);
        $this->assertEquals(AbstractProvider::TYPE_FILE, $media->getType());

        $media->setProviderReference('reference');
        $this->assertEquals('reference', $media->getProviderReference());

        $media->setProviderMetaData(['metadata']);
        $this->assertEquals(['metadata'], $media->getProviderMetaData());

        $media->setImage('image');
        $this->assertEquals('image', $media->getImage());

        $media->setImageMetaData(['image_metadata']);
        $this->assertEquals(['image_metadata'], $media->getImageMetaData());

        $this->assertEquals('50-50', $media->getFocalPoint());
        $media->setFocalPoint('70-30');
        $this->assertEquals('70-30', $media->getFocalPoint());

        $media->setCopyright('MediaMonks');
        $this->assertEquals('MediaMonks', $media->getCopyright());

        $media->setAuthorName('MediaMonks');
        $this->assertEquals('MediaMonks', $media->getAuthorName());

        $media->setBinaryContent('content');
        $this->assertEquals('content', $media->getBinaryContent());

        $createdAt = new DateTimeImmutable();
        $media->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $media->getCreatedAt());

        $updatedAt = new DateTimeImmutable();
        $media->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $media->getUpdatedAt());

        $this->assertEquals(sprintf('Title (%s)', AbstractProvider::TYPE_FILE), (string)$media);
    }
}
