<?php

namespace MediaMonks\SonataMediaBundle\Tests\Model;

class AbstractMediaTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $media = new Media();

        $this->assertInstanceOf(\DateTime::class, $media->getCreatedAt());
        $this->assertInstanceOf(\DateTime::class, $media->getUpdatedAt());

        $media->setId(1);
        $this->assertEquals(1, $media->getId());
        $this->assertEquals(1, $media->getSlug());

        $media->setTitle('Title');
        $this->assertEquals('Title', $media->getTitle());

        $media->setDescription('Description');
        $this->assertEquals('Description', $media->getDescription());

        $media->setProvider('provider');
        $this->assertEquals('provider', $media->getProvider());

        $media->setType('type');
        $this->assertEquals('type', $media->getType());

        $media->setProviderReference('referece');
        $this->assertEquals('referece', $media->getProviderReference());

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

        $createdAt = new \DateTime;
        $media->setCreatedAt($createdAt);
        $this->assertEquals($createdAt, $media->getCreatedAt());

        $updatedAt = new \DateTime;
        $media->setUpdatedAt($updatedAt);
        $this->assertEquals($updatedAt, $media->getUpdatedAt());

        $this->assertEquals('Title (type)', (string)$media);
    }
}
