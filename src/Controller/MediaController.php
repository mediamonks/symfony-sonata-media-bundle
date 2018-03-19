<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use MediaMonks\SonataMediaBundle\Utility\DownloadUtility;
use MediaMonks\SonataMediaBundle\Utility\ImageUtility;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MediaController extends Controller
{
    /**
     * @param Request $request
     * @param int $id
     * @param int $width
     * @param int $height
     * @return RedirectResponse
     */
    public function imageRedirectAction(Request $request, $id, $width, $height)
    {
        return $this->get(ImageUtility::class)->getRedirectResponse(
            $this->getMediaById($id),
            new ImageParameterBag($width, $height, $request->query->all())
        );
    }

    /**
     * @param Request $request
     * @param $id
     * @return StreamedResponse
     */
    public function downloadAction(Request $request, $id)
    {
        return $this->get(DownloadUtility::class)->getStreamedResponse(
            $this->getMediaById($id),
            new DownloadParameterBag($request->query->all())
        );
    }

    /**
     * @param $id
     * @return MediaInterface
     */
    protected function getMediaById($id)
    {
        return $this->getDoctrine()->getManager()->find(
            $this->getParameter('mediamonks.sonata_media.entity.class'),
            $id
        );
    }
}
