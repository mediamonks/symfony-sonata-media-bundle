<?php

namespace MediaMonks\SonataMediaBundle\Service;

use Closure;
use League\Flysystem\FilesystemOperator;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use Symfony\Component\HttpFoundation\HeaderUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\EventListener\AbstractSessionListener;

class MediaResponseHandler
{
    protected ParameterHandlerInterface $parameterHandler;
    protected FilesystemOperator $filesystemPrivate;
    protected ImageGenerator $imageGenerator;
    protected string $mediaBaseUrl;
    protected int $mediaCacheTtl;

    /**
     * @param ParameterHandlerInterface $parameterHandler
     * @param FilesystemOperator $filesystemPrivate
     * @param ImageGenerator $imageGenerator
     * @param string $mediaBaseUrl
     * @param int $mediaCacheTtl
     */
    public function __construct(
        ParameterHandlerInterface $parameterHandler,
        FilesystemOperator $filesystemPrivate,
        ImageGenerator $imageGenerator,
        string $mediaBaseUrl,
        int $mediaCacheTtl
    )
    {
        $this->parameterHandler = $parameterHandler;
        $this->filesystemPrivate = $filesystemPrivate;
        $this->imageGenerator = $imageGenerator;
        $this->mediaBaseUrl = $mediaBaseUrl;
        $this->mediaCacheTtl = $mediaCacheTtl;
    }

    /**
     * @param MediaInterface $media
     * @param MediaParameterBag $parameterBag
     *
     * @return StreamedResponse
     */
    public function getStreamedResponse(MediaInterface $media, MediaParameterBag $parameterBag): StreamedResponse
    {
        $this->parameterHandler->validateParameterBag($media, $parameterBag);
        $filename = $media->getProviderReference();

        $response = new StreamedResponse($this->readStream($filename), 200, [
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => $media->getProviderMetadataValue('mimeType'),
            'Content-Length' => $media->getProviderMetadataValue('size'),
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
     */
    public function getDownloadResponse(MediaInterface $media, MediaParameterBag $parameterBag): StreamedResponse
    {
        $this->parameterHandler->validateParameterBag($media, $parameterBag);
        $filename = $media->getProviderReference();

        return new StreamedResponse($this->readStream($filename), 200, [
            'Content-Disposition' => HeaderUtils::makeDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $media->getProviderMetadataValue('originalName', 'download')),
            'Content-Transfer-Encoding' => 'binary',
            'Content-Type' => $media->getProviderMetadataValue('mimeType'),
            'Content-Length' => $media->getProviderMetadataValue('size'),
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER => true
        ]);
    }

    /**
     * @param MediaInterface $media
     * @param ImageParameterBag $parameterBag
     *
     * @return RedirectResponse
     */
    public function getRedirectResponse(MediaInterface $media, MediaParameterBag $parameterBag): RedirectResponse
    {
        $filename = $media->getProviderReference();

        $response = new RedirectResponse(sprintf('%s%s', $this->mediaBaseUrl, $filename), 302, [
            AbstractSessionListener::NO_AUTO_CACHE_CONTROL_HEADER => true
        ]);
        $response->setSharedMaxAge($this->mediaCacheTtl);
        $response->setMaxAge($this->mediaCacheTtl);

        return $response;
    }

    /**
     * @param string $location
     *
     * @return Closure
     */
    protected function readStream(string $location): Closure
    {
        return function () use ($location) {
            $fileHandle = $this->filesystemPrivate->readStream($location);
            while (!feof($fileHandle)) {
                echo fread($fileHandle, 1024);
            }
            fclose($fileHandle);
        };
    }
}