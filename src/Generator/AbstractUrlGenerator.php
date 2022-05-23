<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
{
    private RouterInterface $router;
    private ParameterHandlerInterface $parameterHandler;
    private string $defaultRouteName;

    /**
     * @param RouterInterface $router
     * @param ParameterHandlerInterface $parameterHandler
     * @param string $defaultRouteName
     */
    public function __construct(
        RouterInterface $router,
        ParameterHandlerInterface $parameterHandler,
        string $defaultRouteName
    )
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
     *
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ParameterBagInterface $parameterBag,
        $routeName = null,
        $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->router->generate(
            $routeName ?? $this->defaultRouteName,
            $this->parameterHandler->getRouteParameters($media, $parameterBag),
            $referenceType
        );
    }
}
