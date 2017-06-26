<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
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
     * @param ParameterBagInterface $parameterBag
     * @param null $routeName
     * @param int $referenceType
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ParameterBagInterface $parameterBag,
        $routeName = null,
        $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ) {
        if (empty($routeName)) {
            $routeName = $this->defaultRouteName;
        }

        return $this->router->generate(
            $routeName,
            $this->parameterHandler->getRouteParameters($media, $parameterBag),
            $referenceType
        );
    }
}
