<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;

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
                    'providers'     => $this->get('mediamonks.media.provider.pool')->getProviders(),
                    'base_template' => $this->getBaseTemplate(),
                    'admin'         => $this->admin,
                    'action'        => 'create',
                ]
            );
        }

        return parent::createAction();
    }
}
