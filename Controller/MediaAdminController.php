<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\HttpFoundation\Request;

class MediaAdminController extends CRUDController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (!$this->getRequest()->get('provider') && $this->getRequest()->isMethod('get')) {
            return $this->render(
                '@MediaMonksSonataMedia/MediaAdmin/select_provider.html.twig',
                [
                    'providers'     => $this->get('mediamonks.sonata_media.provider.pool')->getProviders(),
                    'base_template' => $this->getBaseTemplate(),
                    'admin'         => $this->admin,
                    'action'        => 'create',
                ]
            );
        }

        return parent::createAction();
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function imageRedirectAction(Request $request, $id)
    {
        $media = $this->getDoctrine()->getManager()->find('MediaMonksSonataMediaBundle:Media', $id);

        return $this->get('mediamonks.sonata_media.utility.image')->getRedirectResponse($media, $request);
    }
}
