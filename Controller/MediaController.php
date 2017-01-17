<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends Controller
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function thumbnailAction(Request $request, $id)
    {
        return $this->get('mediamonks.media.helper.controller')->redirectToThumbnail(
            $request,
            $id,
            function ($id) {
                return $this->getDoctrine()->getManager()->find('MediaMonksSonataMediaBundle:Media', $id);
            }
        );
    }
}
