<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterBag;
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
     * @param int $width
     * @param int $height
     * @param array $extra
     * @return RedirectResponse
     */
    public function getRedirectResponse(MediaInterface $media, $width, $height, array $extra = [])
    {
        $response = new RedirectResponse($this->mediaBaseUrl.$this->getFilename($media, $width, $height, $extra));
        $response->setSharedMaxAge($this->cacheTtl);
        $response->setMaxAge($this->cacheTtl);

        return $response;
    }

    /**
     * @param MediaInterface $media
     * @param int $width
     * @param int $height
     * @param array $parameters
     * @return string
     */
    public function getFilename(MediaInterface $media, $width, $height, array $parameters)
    {
        $parameterBag = $this->parameterHandler->getPayload($media->getId(), $width, $height, $parameters);
        $filename = $this->imageGenerator->generate($media, $parameterBag);

        return $filename;
    }
}
