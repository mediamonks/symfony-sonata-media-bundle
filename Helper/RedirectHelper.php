<?php

namespace MediaMonks\SonataMediaBundle\Helper;

use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RedirectHelper
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
     * @param ParameterHandlerInterface $parameterHandler
     * @param ImageGenerator $imageGenerator
     * @param string $mediaBaseUrl
     * @param int $cacheTtl
     */
    public function __construct(
        ParameterHandlerInterface $parameterHandler,
        ImageGenerator $imageGenerator,
        $mediaBaseUrl,
        $cacheTtl
    ) {
        $this->parameterHandler = $parameterHandler;
        $this->imageGenerator = $imageGenerator;
        $this->mediaBaseUrl = $mediaBaseUrl;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param MediaInterface $media
     * @param Request $request
     * @param array $parameters
     * @return RedirectResponse
     */
    public function redirectToMediaImage(MediaInterface $media, Request $request, array $parameters = [])
    {
        $urlParameters = $this->parameterHandler->getPayload($media, $request);
        $parameters = array_merge($parameters, $urlParameters);
        $filename = $this->imageGenerator->generate($media, $parameters);

        $response = new RedirectResponse($this->mediaBaseUrl.$filename);
        $response->setSharedMaxAge($this->cacheTtl);
        $response->setMaxAge($this->cacheTtl);
        return $response;
    }
}
