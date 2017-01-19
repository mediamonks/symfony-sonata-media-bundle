<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class ImageUtility
{
    /**
     * @var ParameterHandlerInterface
     */
    private $parameterHandler;

    /**
     * @var ImageGenerator
     */
    private $imageGenerator;

    /**
     * @var string
     */
    private $mediaBaseUrl;

    /**
     * @var int
     */
    private $cacheTtl;

    /**
     * @var array
     */
    private $defaultParameters;

    /**
     * @param ParameterHandlerInterface $parameterHandler
     * @param ImageGenerator $imageGenerator
     * @param string $mediaBaseUrl
     * @param int $cacheTtl
     * @param array $defaultParameters
     */
    public function __construct(
        ParameterHandlerInterface $parameterHandler,
        ImageGenerator $imageGenerator,
        $mediaBaseUrl,
        $cacheTtl,
        $defaultParameters = []
    ) {
        $this->parameterHandler = $parameterHandler;
        $this->imageGenerator = $imageGenerator;
        $this->mediaBaseUrl = $mediaBaseUrl;
        $this->cacheTtl = $cacheTtl;
        $this->defaultParameters = $defaultParameters;
    }

    /**
     * @param MediaInterface $media
     * @param Request $request
     * @param array $parameters
     * @return RedirectResponse
     */
    public function getRedirectResponse(MediaInterface $media, Request $request, array $parameters = [])
    {
        $response = new RedirectResponse($this->mediaBaseUrl.$this->getFilename($media, $request, $parameters));
        $response->setSharedMaxAge($this->cacheTtl);
        $response->setMaxAge($this->cacheTtl);

        return $response;
    }

    /**
     * @param MediaInterface $media
     * @param Request $request
     * @param array $parameters
     * @return string
     */
    public function getFilename(MediaInterface $media, Request $request, array $parameters)
    {
        $urlParameters = $this->parameterHandler->getPayload($media, $request);
        $parameters = array_merge($this->defaultParameters, $parameters, $urlParameters);
        $filename = $this->imageGenerator->generate($media, $parameters);

        return $filename;
    }
}
