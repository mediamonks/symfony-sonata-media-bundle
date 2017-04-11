<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Model\MediaInterface;
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
        return $this->get('mediamonks.sonata_media.utility.image')->getRedirectResponse(
            $this->getMediaById($id),
            $width,
            $height,
            $request->query->all()
        );
    }

    /**
     * @param $id
     * @return StreamedResponse
     */
    public function downloadAction($id)
    {
        return $this->get('mediamonks.sonata_media.utility.download')->getStreamedResponse($this->getMediaById($id));
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
