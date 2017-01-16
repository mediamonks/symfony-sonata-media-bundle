<?php

namespace MediaMonks\MediaBundle\Provider;

use MediaMonks\MediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Constraints as Constraint;

class YouTubeProvider extends ImageProvider implements ProviderInterface
{
    const URL_OEMBED = 'http://www.youtube.com/oembed?url=http://www.youtube.com/watch?v=%s&format=json';
    const URL_IMAGE_MAX_RES = 'https://i.ytimg.com/vi/%s/maxresdefault.jpg';
    const URL_IMAGE_HQ = 'https://i.ytimg.com/vi/%s/hqdefault.jpg';

    /**
     * @var array
     */
    protected $templates = [
        'helper_media'       => 'MediaMonksMediaBundle:Provider:youtube_media.html.twig',
        'helper_media_admin' => 'MediaMonksMediaBundle:Provider:youtube_media_admin.html.twig',
    ];

    /**
     * @param FormMapper $formMapper
     */
    public function buildEditForm(FormMapper $formMapper)
    {
        $formMapper
            ->with('General')
            ->add('providerName', HiddenType::class)
            ->add('providerReference', TextType::class, ['label' => 'YouTube ID'])
            ->add(
                'binaryContent',
                FileType::class,
                [
                    'required'    => false,
                    'constraints' => [
                        new Constraint\File(),
                    ],
                    'label'       => 'Replacement Image',
                ]
            )
            ->add('title')
            ->add('description')
            ->add('authorName')
            ->add('copyright')
            ->add('tags')
            ->add(
                'pointOfInterest',
                ChoiceType::class,
                [
                    'required' => false,
                    'label'    => 'Point Of Interest',
                    'choices'  => $this->getPointOfInterestChoices(),
                ]
            )
            ->add('featured')
            ->end()
            ->end();
    }

    /**
     * @param FormMapper $formMapper
     */
    public function buildCreateForm(FormMapper $formMapper)
    {
        $formMapper
            ->add('providerName', HiddenType::class)
            ->add('providerReference', TextType::class, ['label' => 'YouTube ID']);
    }

    /**
     * @param MediaInterface $media
     * @throws \Exception
     */
    public function update(MediaInterface $media)
    {
        $currentYoutubeId = $media->getProviderReference();
        $media->setProviderReference($this->parseYouTubeId($media->getProviderReference()));

        if ($currentYoutubeId !== $media->getProviderReference()) {
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

        if (!is_null($media->getBinaryContent())) {
            $this->handleFileUpload($media);
        }
    }

    /**
     * @param MediaInterface $media
     */
    public function refreshThumbnail(MediaInterface $media)
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
        $data = json_decode(
            @file_get_contents(
                sprintf(self::URL_OEMBED, $id)
            ),
            true
        );
        if (empty($data)) {
            throw new \Exception(sprintf('Could not get data from YouTube for id "%s", is the id correct?', $id));
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
            $vid = substr($url['path'], 1);

            return $vid;
        }

        return $value;
    }

    /**
     * @param MediaInterface $media
     * @param array $options
     * @return array
     */
    public function toArray(MediaInterface $media, array $options = [])
    {
        return parent::toArray($media, $options) + [
                'type' => $this->getTypeName(),
                'id'   => $media->getProviderReference(),
            ];
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
    public function getName()
    {
        return 'YouTube Video';
    }

    public function getTypeName()
    {
        return 'youtube';
    }


}