<?php

namespace AppBundle\Controller;

use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{
    /**
     * @Route("/ready")
     */
    public function foobarAction()
    {
        return new Response('MediaMonks Functional Test App Ready');
    }

    /**
     * @Route("/twig")
     * @return Response
     */
    public function twigAction()
    {
        return $this->render(
            'AppBundle:Test:index.html.twig',
            [
                'media' => $this->getDoctrine()->getManager()->find('AppBundle:Media', 1),
            ]
        );
    }

    /**
     * @Route("/api")
     * @return Response
     */
    public function apiAction()
    {
        $media = $this->getDoctrine()->getManager()->find('AppBundle:Media', 1);

        $url = $this->get('mediamonks.sonata_media.generator.url_generator.image')->generate(
            $media,
            new ImageParameterBag(400, 300)
        );

        return new JsonResponse(['url' => $url]);
    }
}
