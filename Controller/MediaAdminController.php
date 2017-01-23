<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Entity\Media;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
                    'providers' => $this->get('mediamonks.sonata_media.provider.pool')->getProviders(),
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ]
            );
        }

        return parent::createAction();
    }

    /**
     * @param Request $request
     * @param $id
     * @return StreamedResponse
     */
    public function downloadAction(Request $request, $id)
    {
        return $this->get('mediamonks.sonata_media.utility.download')->getStreamedResponse($this->getMediaById($id));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function imageAction(Request $request, $id)
    {
        return $this->get('mediamonks.sonata_media.utility.image')->getRedirectResponse(
            $this->getMediaById($id),
            $request
        );
    }

    /**
     * @param $id
     * @return MediaInterface
     */
    protected function getMediaById($id)
    {
        return $this->getDoctrine()->getManager()->find('MediaMonksSonataMediaBundle:Media', $id);
    }
}
