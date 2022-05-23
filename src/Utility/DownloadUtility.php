<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use League\Flysystem\FilesystemOperator;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadUtility
{
    private ParameterHandlerInterface $parameterHandler;
    private FilesystemOperator $filesystem;

    /**
     * @param ParameterHandlerInterface $parameterHandler
     * @param FilesystemOperator $filesystem
     */
    public function __construct(ParameterHandlerInterface $parameterHandler, FilesystemOperator $filesystem)
    {
        $this->parameterHandler = $parameterHandler;
        $this->filesystem = $filesystem;
    }

    /**
     * @param MediaInterface $media
     * @param DownloadParameterBag $parameterBag
     *
     * @return StreamedResponse
     */
    public function getStreamedResponse(MediaInterface $media, DownloadParameterBag $parameterBag): StreamedResponse
    {
        $this->parameterHandler->validateParameterBag($media, $parameterBag);

        $response = new StreamedResponse(function () use ($media) {
            $fileHandle = $this->filesystem->readStream($media->getProviderReference());
            while (!feof($fileHandle)) {
                echo fread($fileHandle, 1024);
            }
            fclose($fileHandle);
        });
        $response->headers->set('Content-Disposition', $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $media->getProviderMetaData()['originalName']
        ));
        if (!empty($media->getProviderMetaData()['mimeType'])) {
            $response->headers->set('Content-Type', $media->getProviderMetaData()['mimeType']);
        }

        return $response;
    }
}
