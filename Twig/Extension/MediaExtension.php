<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

use MediaMonks\SonataMediaBundle\Provider\ProviderInterface;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Helper\Parameter;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;

class MediaExtension extends \Twig_Extension
{
    /**
     * @var ProviderPool
     */
    protected $providerPool;

    /**
     * @var Parameter
     */
    protected $parameterHelper;

    /**
     * MediaExtension constructor.
     * @param Parameter $parameter
     */
    public function __construct(ProviderPool $providerPool, Parameter $parameter)
    {
        $this->providerPool = $providerPool;
        $this->parameterHelper = $parameter;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'media';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter(
                'media', [$this, 'media'], [
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_image', [$this, 'mediaImage'], [
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_type', [$this, 'mediaType'], [
                    'is_safe'           => ['html'],
                ]
            ),
        ];
    }

    /**
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    public function media(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        $routeName = null,
        array $parameters = []
    ) {
        return $environment->render(
            $this->getProviderByMedia($media)->getMediaTemplate(),
            [
                'media'      => $media,
                'width'      => $width,
                'height'     => $height,
                'routeName'  => $routeName,
                'parameters' => $parameters,
            ]
        );
    }

    /**
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param string $routeName
     * @param array $parameters
     * @return string
     */
    public function mediaImage(
        MediaInterface $media,
        $width,
        $height,
        $routeName = null,
        array $parameters = []
    ) {
        $parameters += [
            'w' => $width,
            'h' => $height,
        ];

        return sprintf(
            '<img src="%s" width="%d" height="%d" title="%s">',
            $this->parameterHelper->generateUrl($media, $parameters, $routeName),
            $width,
            $height,
            $media->getTitle()
        );
    }

    /**
     * @param MediaInterface $media
     * @return string
     */
    public function mediaType(MediaInterface $media)
    {
        return $this->getProviderByMedia($media)->getName();
    }

    /**
     * @param MediaInterface $media
     * @return ProviderInterface
     */
    private function getProviderByMedia(MediaInterface $media)
    {
        return $this->providerPool->getProvider($media->getProviderName());
    }
}
