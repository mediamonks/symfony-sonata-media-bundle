<?php

namespace MediaMonks\SonataMediaBundle\Generator;

use InvalidArgumentException;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface as SymfonyUrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
{
    const ROUTE_IMAGE_STREAM = 'imageStream';
    const ROUTE_IMAGE_DOWNLOAD = 'imageDownload';
    const ROUTE_IMAGE_REDIRECT = 'imageRedirect';
    const ROUTE_STREAM = 'stream';
    const ROUTE_DOWNLOAD = 'download';
    const ROUTE_REDIRECT = 'redirect';

    const ROUTE_KEYS = [
        self::ROUTE_IMAGE_STREAM,
        self::ROUTE_IMAGE_DOWNLOAD,
        self::ROUTE_IMAGE_REDIRECT,
        self::ROUTE_STREAM,
        self::ROUTE_DOWNLOAD,
        self::ROUTE_REDIRECT
    ];

    private RouterInterface $router;
    private ParameterHandlerInterface $parameterHandler;
    private array $defaultRoutes;

    /**
     * @param RouterInterface $router
     * @param ParameterHandlerInterface $parameterHandler
     * @param array $defaultRoutes
     */
    public function __construct(
        RouterInterface $router,
        ParameterHandlerInterface $parameterHandler,
        array $defaultRoutes
    )
    {
        $this->router = $router;
        $this->parameterHandler = $parameterHandler;
        $this->defaultRoutes = $defaultRoutes;
    }

    /**
     * @param MediaInterface $media
     * @param ParameterBagInterface $parameterBag
     * @param string $routeName
     * @param int $referenceType
     *
     * @return string
     */
    public function generate(
        MediaInterface $media,
        ParameterBagInterface $parameterBag,
        string $routeName,
        int $referenceType = SymfonyUrlGeneratorInterface::ABSOLUTE_PATH
    ): string
    {
        return $this->router->generate(
            $routeName,
            $this->parameterHandler->getRouteParameters($media, $parameterBag),
            $referenceType
        );
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function getRoute(string $key): string
    {
        if (!isset($this->defaultRoutes[$key])) {
            throw new  InvalidArgumentException(sprintf('%s is not a valid route key. Available keys: [%s]', $key, implode(', ', self::ROUTE_KEYS)));
        }

        return $this->defaultRoutes[$key];
    }
}
