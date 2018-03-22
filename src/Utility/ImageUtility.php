<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ParameterBagInterface;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

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
     * @param ParameterHandlerInterface $parameterHandler
     * @param ImageGenerator $imageGenerator
     * @param string $mediaBaseUrl
     * @param int $cacheTtl
     */
    public function __construct(
        ParameterHandlerInterface $parameterHandler,
        ImageGenerator $imageGenerator,
        string $mediaBaseUrl,
        int $cacheTtl
    ) {
        $this->parameterHandler = $parameterHandler;
        $this->imageGenerator = $imageGenerator;
        $this->mediaBaseUrl = $mediaBaseUrl;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     * @return RedirectResponse
     */
    public function getRedirectResponse(MediaInterface $media, ImageParameterBag $parameterBag): RedirectResponse
    {
        $response = new RedirectResponse($this->mediaBaseUrl.$this->getFilename($media, $parameterBag));
        $response->setSharedMaxAge($this->cacheTtl);
        $response->setMaxAge($this->cacheTtl);

        return $response;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     * @return string
     */
    public function getFilename(MediaInterface $media, ImageParameterBag $parameterBag): string
    {
        $parameterBag = $this->parameterHandler->validateParameterBag($media, $parameterBag);
        $filename = $this->imageGenerator->generate($media, $parameterBag);

        return $filename;
    }
}
