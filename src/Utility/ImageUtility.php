<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class ImageUtility
{
    private ParameterHandlerInterface $parameterHandler;
    private ImageGenerator $imageGenerator;
    private string $mediaBaseUrl;
    private int $cacheTtl;

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
    )
    {
        $this->parameterHandler = $parameterHandler;
        $this->imageGenerator = $imageGenerator;
        $this->mediaBaseUrl = $mediaBaseUrl;
        $this->cacheTtl = $cacheTtl;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     *
     * @return RedirectResponse
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function getRedirectResponse(MediaInterface $media, ImageParameterBag $parameterBag): RedirectResponse
    {
        $response = new RedirectResponse($this->mediaBaseUrl . $this->getFilename($media, $parameterBag));
        $response->setSharedMaxAge($this->cacheTtl);
        $response->setMaxAge($this->cacheTtl);
        $response->headers->set(AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER, true);

        return $response;
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     *
     * @return string
     * @throws \League\Flysystem\FilesystemException
     * @throws FilesystemException
     */
    public function getFilename(MediaInterface $media, ImageParameterBag $parameterBag): string
    {
        $this->parameterHandler->validateParameterBag($media, $parameterBag);

        return $this->imageGenerator->generate($media, $parameterBag);
    }
}
