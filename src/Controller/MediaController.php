<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use Doctrine\Persistence\ManagerRegistry;
use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use MediaMonks\SonataMediaBundle\Service\ImageResponseHandler;
use MediaMonks\SonataMediaBundle\Service\MediaResponseHandler;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController
{
    private ManagerRegistry $registry;
    private MediaResponseHandler $mediaResponseHandler;
    private ImageResponseHandler $imageResponseHandler;
    private string $mediaEntityClass;

    public function __construct(
        ManagerRegistry $registry,
        MediaResponseHandler $mediaResponseHandler,
        ImageResponseHandler $imageResponseHandler,
        string $mediaEntityClass
    )
    {
        $this->registry = $registry;
        $this->mediaResponseHandler = $mediaResponseHandler;
        $this->imageResponseHandler = $imageResponseHandler;
        $this->mediaEntityClass = $mediaEntityClass;
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return StreamedResponse
     */
    public function streamAction(
        Request $request,
        int $id
    ): StreamedResponse
    {
        return $this->mediaResponseHandler->getStreamedResponse(
            $this->getMediaById($id),
            new MediaParameterBag($request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return StreamedResponse
     */
    public function downloadAction(
        Request $request,
        int $id
    ): StreamedResponse
    {
        return $this->mediaResponseHandler->getStreamedResponse(
            $this->getMediaById($id),
            new MediaParameterBag($request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $width
     * @param int $height
     *
     * @return StreamedResponse
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function imageStreamAction(
        Request $request,
        int $id,
        int $width,
        int $height
    ): StreamedResponse
    {
        return $this->imageResponseHandler->getStreamedResponse(
            $this->getMediaById($id),
            new ImageParameterBag($width, $height, $request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $width
     * @param int $height
     *
     * @return StreamedResponse
     * @throws FilesystemException
     * @throws \League\Flysystem\FilesystemException
     */
    public function imageDownloadAction(
        Request $request,
        int $id,
        int $width,
        int $height
    ): StreamedResponse
    {
        return $this->imageResponseHandler->getDownloadResponse(
            $this->getMediaById($id),
            new ImageParameterBag($width, $height, $request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param int $id
     * @param int $width
     * @param int $height
     *
     * @return RedirectResponse
     * @throws \League\Flysystem\FilesystemException
     * @throws FilesystemException
     */
    public function imageRedirectAction(
        Request $request,
        int $id,
        int $width,
        int $height
    ): RedirectResponse
    {
        return $this->imageResponseHandler->getRedirectResponse(
            $this->getMediaById($id),
            new ImageParameterBag($width, $height, $request->query->all())
        );
    }

    /**
     * @param int $id
     *
     * @return MediaInterface
     */
    protected function getMediaById(int $id): MediaInterface
    {
//        $entityClass = $this->getParameter('mediamonks.sonata_media.entity.class');

        return $this->registry->getManager()->getRepository($this->mediaEntityClass)->find($id);
    }
}
