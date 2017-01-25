<?php

namespace MediaMonks\SonataMediaBundle\Provider;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;

class YouTubeProvider extends AbstractProvider implements ProviderInterface
{
    const URL_OEMBED = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json';
    const URL_IMAGE_MAX_RES = 'https://i.ytimg.com/vi/%s/maxresdefault.jpg';
    const URL_IMAGE_HQ = 'https://i.ytimg.com/vi/%s/hqdefault.jpg';

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderCreateForm(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => 'YouTube ID']);
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildProviderEditForm(FormMapper $formMapper)
    {
        $formMapper->add('providerReference', TextType::class, ['label' => 'YouTube ID']);
    }

    /**
     * @param ErrorElement $errorElement
     * @param Media $media
     */
    public function validate(ErrorElement $errorElement, Media $media)
    {
        try {
            $this->getDataByYouTubeId($this->parseYouTubeId($media->getProviderReference()));
        }
        catch (\Exception $e) {
            $errorElement->with('providerReference')->addViolation($e->getMessage());
        }
    }

    /**
     * @param Media $media
     * @param bool $providerReferenceUpdated
     */
    public function update(Media $media, $providerReferenceUpdated)
    {
        if ($providerReferenceUpdated) {
            $media->setProviderReference($this->parseYouTubeId($media->getProviderReference()));

            if ($media->getProviderReference()) {
                $data = $this->getDataByYouTubeId($media->getProviderReference());

                if (empty($media->getTitle())) {
                    $media->setTitle($data['title']);
                }
                if (empty($media->getAuthorName())) {
                    $media->setAuthorName($data['author_name']);
                }

                if (empty($media->getImage())) {
                    $this->refreshThumbnail($media);
                }
            }
        }

        parent::update($media, $providerReferenceUpdated);
    }

    /**
     * @param Media $media
     */
    public function refreshThumbnail(Media $media)
    {
        $filename = sprintf('%s_%d.%s', sha1($media->getProviderReference()), time(), 'jpg');
        $thumbnailUrl = $this->getThumbnailUrlByYouTubeId($media->getProviderReference());
        $this->getFilesystem()->write(
            $filename,
            file_get_contents($thumbnailUrl)
        );
        $media->setImage($filename);
    }

    /**
     * @param string $id
     * @return string
     */
    public function getThumbnailUrlByYouTubeId($id)
    {
        // try to get max res image (only available for 720P videos)
        $urlMaxRes = sprintf(self::URL_IMAGE_MAX_RES, $id);
        stream_context_set_default(['http' => ['method' => 'HEAD']]);
        $headers = get_headers($urlMaxRes);
        stream_context_set_default(['http' => ['method' => 'GET']]);
        if ((int)substr($headers[0], 9, 3) === Response::HTTP_OK) {
            return $urlMaxRes;
        }

        return sprintf(self::URL_IMAGE_HQ, $id); // this one always exists
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    protected function getDataByYouTubeId($id)
    {
        $this->disableErrorHandler();
        $data = json_decode(file_get_contents(sprintf(self::URL_OEMBED, $id)), true);
        $this->restoreErrorHandler();

        if (empty($data['title'])) {
            throw new \Exception(sprintf('YouTube ID "%s" seems to be incorrect', $id));
        }

        return $data;
    }

    /**
     * @param $value
     * @return string
     * @throws \Exception
     */
    protected function parseYouTubeId($value)
    {
        if (strpos($value, 'youtube.com')) {
            $url = parse_url($value);
            if (empty($url['query'])) {
                throw new \Exception('The supplied URL does not look like a Youtube URL');
            }
            parse_str($url['query'], $params);
            if (empty($params['v'])) {
                throw new \Exception('The supplied URL does not look like a Youtube URL');
            }

            return $params['v'];
        }

        if (strpos($value, 'youtu.be')) {
            $url = parse_url($value);
            $id = substr($url['path'], 1);

            return $id;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return 'youtube-play';
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return 'YouTube Video';
    }

    public function getType()
    {
        return 'youtube';
    }

    /**
     * @return string
     */
    public function getEmbedTemplate()
    {
        return 'MediaMonksSonataMediaBundle:Provider:youtube_embed.html.twig';
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
