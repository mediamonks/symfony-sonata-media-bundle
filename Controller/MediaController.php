<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class MediaController extends Controller
{
    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageAction(Request $request, $id)
    {
        $media = $this->getDoctrine()->getManager()->find('MediaMonksSonataMediaBundle:Media', $id);
        $parameters = $this->get('mediamonks.sonata_media.handler.signature_parameter_handler')->getPayload($media, $request);
        // @todo apply default parameters?
        // @todo apply media parameters
        $filename = $this->get('mediamonks.sonata_media.generator.image')->generate($media, $parameters);

        // @todo wrap into service
        $response = new RedirectResponse('http://localhost/mediamonks-sonata-media/web/data/'.$filename);
        // @todo set cache headers
        return $response;
    }
}
