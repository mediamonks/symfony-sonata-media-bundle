<?php

namespace MediaMonks\MediaBundle\Helper;

use MediaMonks\MediaBundle\Model\MediaInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class Parameter
{
    const ROUTE_NAME_PUBLIC = 'public';
    const ROUTE_NAME_PRIVATE = 'private';

    const PARAMETER_ID = 'id';
    const PARAMETER_SIGNATURE = 's';
    const PARAMETER_VERSION = 'v';
    const PARAMETER_ANTI_CACHE = 'ac'; // allow cache busting by adding &ac=<random>

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $routeNames;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $destinationPrefix;

    /**
     * @var string
     */
    protected $hashAlgorithm = 'sha256';

    /**
     * UrlGenerator constructor.
     * @param Router $router
     * @param array $routeNames
     * @param string $secret
     * @param string $destinationPrefix
     */
    public function __construct(Router $router, array $routeNames, $secret, $destinationPrefix = 'thumbs/')
    {
        $this->router = $router;
        $this->secret = $secret;
        $this->destinationPrefix = $destinationPrefix;

        $this->setRouteNames($routeNames);
    }

    /**
     * @param array $routeNames
     * @throws \Exception
     */
    protected function setRouteNames(array $routeNames)
    {
        if (!array_key_exists(self::ROUTE_NAME_PUBLIC, $routeNames)) {
            throw new \Exception(sprintf('Route name "%s" is required', self::ROUTE_NAME_PUBLIC));
        }
        if (!array_key_exists(self::ROUTE_NAME_PRIVATE, $routeNames)) {
            throw new \Exception(sprintf('Route name "%s" is required', self::ROUTE_NAME_PRIVATE));
        }
        $this->routeNames = $routeNames;
    }

    /**
     * @param MediaInterface $media
     * @param array $parameters
     * @param string $route
     * @return string
     */
    public function generateUrl(MediaInterface $media, $parameters, $route = self::ROUTE_NAME_PUBLIC)
    {
        return $this->router->generate(
            $this->routeNames[$route],
            $this->signParameters($media->getDefaultUrlParameters() + $parameters),
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function signParameters(array $parameters)
    {
        $parameters['s'] = $this->calculateSignature($parameters);

        return $parameters;
    }

    /**
     * @param array $parameters
     * @return string
     */
    protected function calculateSignature(array $parameters)
    {
        return hash_hmac($this->hashAlgorithm, $this->secret, json_encode($this->normalizeParameters($parameters)));
    }

    /**
     * @param array $parameters
     * @return array
     */
    protected function normalizeParameters(array $parameters)
    {
        if (isset($parameters[self::PARAMETER_SIGNATURE])) {
            unset($parameters[self::PARAMETER_SIGNATURE]);
        }
        if (isset($parameters[self::PARAMETER_ANTI_CACHE])) {
            unset($parameters[self::PARAMETER_ANTI_CACHE]);
        }
        ksort($parameters);

        $parametersNormalized = [];
        foreach ($parameters as $k => $v) {
            $parametersNormalized[$k] = (string)$v;
        }

        return $parametersNormalized;
    }

    /**
     * @param array $parameters
     * @return bool
     */
    public function isValid(array $parameters)
    {
        if (!hash_equals($this->calculateSignature($parameters), $parameters[self::PARAMETER_SIGNATURE])) {
            return false;
        }

        return true;
    }

    /**
     * @param array $parameters
     * @return string
     */
    protected function getFormat(array $parameters)
    {
        if (isset($parameters['fm'])) {
            return $parameters['fm'];
        }

        return 'jpg';
    }

    /**
     * @param $source
     * @param $parameters
     * @return string
     */
    public function getDestinationFilename($source, $parameters)
    {
        $parameters = $this->normalizeParameters($parameters);

        $parametersFlat = [];
        foreach ($parameters as $k => $v) {
            $parametersFlat[] = $k.$v;
        }

        return $this->destinationPrefix.
            pathinfo($source, PATHINFO_FILENAME).'/'.implode('_', $parametersFlat).'.'.$this->getFormat($parameters);
    }
}