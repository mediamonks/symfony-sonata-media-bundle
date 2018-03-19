<?php

namespace MediaMonks\SonataMediaBundle\Tests\AppBundle\Controller;

use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Tests\AppBundle\Entity\Media;
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
                'media' => $this->getDoctrine()->getManager()->find(Media::class, 1),
            ]
        );
    }

    /**
     * @Route("/api")
     * @return Response
     */
    public function apiAction()
    {
        $media = $this->getDoctrine()->getManager()->find(Media::class, 1);

        $url = $this->get(ImageGenerator::class)->generate(
            $media,
            new ImageParameterBag(400, 300)
        );

        return new JsonResponse(['url' => $url]);
    }
}
