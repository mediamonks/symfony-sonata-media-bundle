<?php

namespace MediaMonks\SonataMediaBundle\Tests\Functional\src\Controller;

use Doctrine\Persistence\ManagerRegistry;
use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\Generator\ImageGenerator;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Tests\Functional\src\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class TestController extends AbstractController
{
    private ManagerRegistry $registry;
    private ImageGenerator $imageGenerator;
    private Environment $twig;

    public function __construct(
        ManagerRegistry $registry,
        ImageGenerator $imageGenerator,
        Environment $twig
    )
    {
        $this->registry = $registry;
        $this->imageGenerator = $imageGenerator;
        $this->twig = $twig;
    }

    /**
     * @Route("/ready")
     */
    public function foobarAction(): Response
    {
        return new Response('MediaMonks Functional Test App Ready');
    }

    /**
     * @Route("/twig")
     *
     * @return Response
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function twigAction(): Response
    {
        return new Response($this->twig->render(
            'test/index.html.twig',
            [
                'media' => $this->registry->getManager()->find(Media::class, 1),
            ]
        ));
    }

    /**
     * @Route("/api")
     *
     * @return Response
     * @throws \League\Flysystem\FilesystemException
     * @throws FilesystemException
     */
    public function apiAction()
    {
        $media = $this->registry->getManager()->find(Media::class, 1);
        $url = $this->imageGenerator->generate(
            $media,
            new ImageParameterBag(400, 300)
        );

        return new JsonResponse(['url' => $url]);
    }
}
