<?php

namespace MediaMonks\SonataMediaBundle\Utility;

use League\Flysystem\Filesystem;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadUtility
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param MediaInterface $media
     * @return StreamedResponse
     */
    public function getStreamedResponse(MediaInterface $media)
    {
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