<?php

namespace MediaMonks\SonataMediaBundle\Service;

use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Exception\InvalidArgumentException;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class ImageResponseHandler extends MediaResponseHandler
{
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
        // this will also generate the image if not already generated
        $filename = $this->generateImageFilename($media, $parameterBag);

        $response = new RedirectResponse(sprintf('%s%s', $this->mediaBaseUrl, $filename), 302, [
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER => true
        ]);
        $response->setSharedMaxAge($this->mediaCacheTtl);
        $response->setMaxAge($this->mediaCacheTtl);

        return $response;
    }

    /**
     * @param MediaInterface $media
     * @param MediaParameterBag $parameterBag
     *
     * @return StreamedResponse
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function getDownloadResponse(MediaInterface $media, MediaParameterBag $parameterBag): StreamedResponse
    {
        if (!$parameterBag instanceof ImageParameterBag) {
            throw InvalidArgumentException::from(static::class, __FUNCTION__, ImageParameterBag::class, get_class($parameterBag));
        }
        $this->parameterHandler->validateParameterBag($media, $parameterBag);

        // this will also generate the image if not already generated
        $filename = $this->generateImageFilename($media, $parameterBag);

        return new StreamedResponse($this->readStream($filename), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $media->getImageMetadataValue('originalName', 'download')),
            'Content-Transfer-Encoding', 'binary',
            'Content-Type' => $media->getImageMetadataValue('mimeType'),
            'Content-Length' => $media->getImageMetadataValue('size'),
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER => true
        ]);
    }

    /**
     * @param MediaInterface $media
     * @param MediaParameterBag $parameterBag
     *
     * @return StreamedResponse
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function getStreamedResponse(MediaInterface $media, MediaParameterBag $parameterBag): StreamedResponse
    {
        if (!$parameterBag instanceof ImageParameterBag) {
            throw InvalidArgumentException::from(static::class, __FUNCTION__, ImageParameterBag::class, get_class($parameterBag));
        }
        $this->parameterHandler->validateParameterBag($media, $parameterBag);

        // this will also generate the image if not already generated
        $filename = $this->generateImageFilename($media, $parameterBag);

        $response = new StreamedResponse($this->readStream($filename), 200, [
            'Content-Transfer-Encoding', 'binary',
            'Content-Type' => $media->getImageMetadataValue('mimeType'),
            'Content-Length' => $media->getImageMetadataValue('size'),
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER => true
        ]);
        $response->setSharedMaxAge($this->mediaCacheTtl);
        $response->setMaxAge($this->mediaCacheTtl);

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
    public function generateImageFilename(MediaInterface $media, ImageParameterBag $parameterBag): string
    {
        $this->parameterHandler->validateParameterBag($media, $parameterBag);

        return $this->imageGenerator->generate($media, $parameterBag);
    }
}