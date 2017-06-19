<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CRUDController extends BaseCRUDController
{
    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        if (!$this->getRequest()->get('provider') && $this->getRequest()->isMethod('get')) {
            return $this->render(
                '@MediaMonksSonataMedia/CRUD/select_provider.html.twig',
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
        $object = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $object);

        return $this->get('mediamonks.sonata_media.utility.download')->getStreamedResponse($object, new DownloadParameterBag($request->query->all()));
    }

    /**
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function imageAction(Request $request, $id, $width, $height)
    {
        $object = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $object);

        return $this->get('mediamonks.sonata_media.utility.image')->getRedirectResponse($object, new ImageParameterBag($width, $height, $request->query->all()));
    }
}
