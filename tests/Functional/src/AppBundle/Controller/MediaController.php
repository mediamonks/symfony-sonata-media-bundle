<?php

namespace MediaMonks\SonataMediaBundle\Tests\AppBundle\Controller;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController extends AbstractController
{
    private ?Filesystem $filesystemPublic = null;

    /**
     * @param Request $request
     *
     * @return Response
     * @throws FilesystemException
     */
    public function readAction(Request $request): Response
    {
        $location = $this->getLocation($request);
        if (!$this->getFilesystemPublic()->fileExists($location)) {
            throw new NotFoundHttpException(sprintf('Resource not found "%s"', $request->getPathInfo()));
        }

        return new Response($this->getFilesystemPublic()->read($location));
    }

    protected function getFilesystemPublic(): Filesystem
    {
        if ($this->filesystemPublic === null) {
            $this->filesystemPublic = $this->get('oneup_flysystem.media_public_filesystem');
        }

        return $this->filesystemPublic;
    }

    protected function getLocation(Request $request)
    {
        $pathInfo = $request->getPathInfo();

        // remove the /media part
        return substr($pathInfo, 6);
    }
}
