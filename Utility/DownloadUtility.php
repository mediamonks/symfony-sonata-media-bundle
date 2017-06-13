<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Handler\DownloadParameterBag;
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
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param ParameterHandlerInterface $parameterHandler
     * @param Filesystem $filesystem
     */
    public function __construct(ParameterHandlerInterface $parameterHandler, Filesystem $filesystem)
    {
        $this->parameterHandler = $parameterHandler;
        $this->filesystem = $filesystem;
    }

    /**
     * @param MediaInterface $media
     * @return StreamedResponse
     */
    public function getStreamedResponse(MediaInterface $media, DownloadParameterBag $parameterBag)
    {
        $this->parameterHandler->verifyParameterBag($media, $parameterBag);

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
