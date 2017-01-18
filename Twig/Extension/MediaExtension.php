<?php

namespace MediaMonks\SonataMediaBundle\Twig\Extension;

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
                    'needs_environment' => true,
                    'is_safe'           => ['html'],
                ]
            ),
            new \Twig_SimpleFilter(
                'media_type', [$this, 'mediaType'], [
                    'needs_environment' => true,
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
     * @param string $type
     * @param array $parameters
     * @return string
     */
    public function media(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        $type = Parameter::ROUTE_NAME_DEFAULT,
        array $parameters = []
    ) {
        $provider = $this->providerPool->getProvider($media->getProviderName());

        return $environment->render(
            $provider->getMediaTemplate(),
            [
                'media'      => $media,
                'width'      => $width,
                'height'     => $height,
                'type'       => $type,
                'parameters' => $parameters,
            ]
        );
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
    public function mediaImage(
        \Twig_Environment $environment,
        MediaInterface $media,
        $width,
        $height,
        $routeName = Parameter::ROUTE_NAME_DEFAULT,
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
     * @param \Twig_Environment $environment
     * @param MediaInterface $media
     */
    public function mediaType(\Twig_Environment $environment, MediaInterface $media)
    {
        return $this->providerPool->getProvider($media->getProviderName())->getName();
    }
}
