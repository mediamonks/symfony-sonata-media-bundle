<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use League\Flysystem\FilesystemInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\Handler\ParameterHandlerInterface;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadUtility
{
    /**
     * @var ParameterHandlerInterface
     */
    private $parameterHandler;

    /**
     * @var FilesystemInterface
     */
    private $filesystem;

    /**
     * @param ParameterHandlerInterface $parameterHandler
     * @param FilesystemInterface $filesystem
     */
    public function __construct(ParameterHandlerInterface $parameterHandler, FilesystemInterface $filesystem)
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

        return $response;
    }
}
