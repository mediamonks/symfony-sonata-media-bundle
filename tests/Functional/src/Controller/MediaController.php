<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional\src\Controller;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MediaController
{
    private Filesystem $filesystemPublic;

    /**
     * @param Filesystem $filesystemPublic
     */
    public function __construct(Filesystem $filesystemPublic)
    {
        $this->filesystemPublic = $filesystemPublic;
    }

    /**
     * @param Request $request
     *
     * @return Response
     * @throws FilesystemException
     */
    public function readAction(Request $request): Response
    {
        $location = $this->getLocation($request);
        if (!$this->filesystemPublic->fileExists($location)) {
            throw new NotFoundHttpException(sprintf('Resource not found "%s"', $request->getPathInfo()));
        }

        return new Response($this->filesystemPublic->read($location));
    }

    protected function getLocation(Request $request)
    {
        $pathInfo = $request->getPathInfo();

        // remove the /media part
        return substr($pathInfo, 6);
    }
}
