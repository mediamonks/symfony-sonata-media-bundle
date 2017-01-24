<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class SoundCloudProvider extends AbstractProvider implements ProviderInterface
{
    const URL_OEMBED = 'https://soundcloud.com/oembed?format=json&url=https://soundcloud.com/%s';
    const URL = 'https://soundcloud.com/%s';

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => 'SoundCloud URL']);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => 'SoundCloud URL']);
    }

    /**
     * @param Media $media
     * @throws \Exception
     */
    public function update(Media $media)
    {
        $currentSoundCloudId = $media->getProviderReference();
        $media->setProviderReference($this->parseReference($media->getProviderReference()));

        if ($currentSoundCloudId !== $media->getProviderReference()) {
            $data = $this->getDataByReference($media->getProviderReference());

            $data['embedUrl'] = $this->extractEmbedUrl($data);
            $media->setProviderMetaData($data);

            if (empty($media->getTitle())) {
                $media->setTitle($data['title']);
            }
            if (empty($media->getDescription())) {
                $media->setDescription($data['description']);
            }
            if (empty($media->getAuthorName())) {
                $media->setAuthorName($data['author_name']);
            }
            if (empty($media->getImage())) {
                $this->setImage($media, $data['thumbnail_url']);
            }
        }

        parent::update($media);
    }

    /**
     * @param Media $media
     * @param $thumbnailUrl
     */
    public function setImage(Media $media, $thumbnailUrl)
    {
        $filename = sprintf('%s_%d.%s', sha1($media->getProviderReference()), time(), 'jpg');
        $this->getFilesystem()->write(
            $filename,
            file_get_contents($thumbnailUrl)
        );
        $media->setImage($filename);
    }

    /**
     * @param $reference
     * @return mixed
     * @throws \Exception
     */
    protected function getDataByReference($reference)
    {
        $this->disableErrorHandler();
        $data = json_decode(file_get_contents(sprintf(self::URL_OEMBED, $reference)), true);
        $this->restoreErrorHandler();

        if (empty($data['title'])) {
            throw new \Exception(
                sprintf('Could not get data from SoundCloud for id "%s", is the name correct?', $reference)
            );
        }

        return $data;
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function parseReference($value)
    {
        if (strpos($value, 'soundcloud.com')) {
            $url = parse_url($value);

            return trim($url['path'], '/');
        }

        return $value;
    }

    /**
     * @param array $data
     */
    protected function extractEmbedUrl(array $data)
    {
        preg_match('/src="(.*)"/', $data['html'], $matches);
        $url = $matches[1];

        $data = parse_url($url);
        parse_str($data['query'], $data);

        return $data['url'];
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'soundcloud';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'SoundCloud';
    }

    public function getType()
    {
        return 'audio';
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:soundcloud_embed.html.twig';
    }

    /**
     * @return bool
     */
    public function supportsDownload()
    {
        return false;
    }

    /**
     * @return bool
     */
    public function supportsEmbed()
    {
        return true;
    }

    /**
     * @return bool
     */
    public function supportsImage()
    {
        return true;
    }
}
