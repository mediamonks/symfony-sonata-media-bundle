<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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
}
