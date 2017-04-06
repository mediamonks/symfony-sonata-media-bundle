<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UrlGenerator
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var ParameterHandlerInterface
     */
    private $parameterHandler;

    /**
     * @var string
     */
    private $defaultRouteName;

    /**
     * @param Router $router
     * @param ParameterHandlerInterface $parameterHandler
     * @param $defaultRouteName
     */
    public function __construct(Router $router, ParameterHandlerInterface $parameterHandler, $defaultRouteName)
    {
        $this->router = $router;
        $this->parameterHandler = $parameterHandler;
        $this->defaultRouteName = $defaultRouteName;
    }

    /**
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param array $extra
     * @param null $routeName
     * @param int $referenceType
     * @return string
     */
    public function generate(
        MediaInterface $media,
        $width,
        $height,
        array $extra = [],
        $routeName = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        if (empty($routeName)) {
            $routeName = $this->defaultRouteName;
        }

        if (!isset($extra['fit']) && !empty($media->getFocalPoint())) {
            $extra['fit'] = sprintf('crop-%s', $media->getFocalPoint());
        }

        return $this->router->generate(
                $routeName,
                $this->parameterHandler->getRouteParameters(new ParameterBag($media->getId(), $width, $height, $extra)),
                $referenceType
            );
    }
}
