<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
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
     * @param array $parameters
     * @param null $routeName
     * @param int $referenceType
     * @return string
     */
    public function generate(
        MediaInterface $media,
        array $parameters,
        $routeName = null,
        $referenceType = UrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        if (empty($routeName)) {
            $routeName = $this->defaultRouteName;
        }

        return $this->router->generate(
            $routeName,
            $this->parameterHandler->getQueryString($media, $parameters),
            $referenceType
        );
    }
}
