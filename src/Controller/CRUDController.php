<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\ParameterBag\DownloadParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Utility\DownloadUtility;
use MediaMonks\SonataMediaBundle\Utility\ImageUtility;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CRUDController extends BaseCRUDController
{
    /** @inheritDoc */
    public function createAction(): Response
    {
        $request = $this->getRequest();
        if (!$this->getRequest()->get('provider') && $this->getRequest()->isMethod('get')) {
            $types = $request->query->get('types');
            if (is_array($types)) {
                $providers = $this->get(ProviderPool::class)->getProvidersByTypes($types);
            } else {
                $providers = $this->get(ProviderPool::class)->getProviders();
            }

            return $this->renderWithExtraParams(
                '@MediaMonksSonataMedia/CRUD/select_provider.html.twig',
                [
                    'providers' => $providers,
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
     * @param string|int $id
     *
     * @return StreamedResponse
     */
    public function downloadAction(Request $request, $id): StreamedResponse
    {
        $object = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $object);

        return $this->get(DownloadUtility::class)->getStreamedResponse($object, new DownloadParameterBag($request->query->all()));
    }

    /**
     * @param Request $request
     * @param string|int $id
     * @param int $width
     * @param int $height
     *
     * @return RedirectResponse
     */
    public function imageAction(Request $request, $id, int $width, int $height): RedirectResponse
    {
        $object = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $object);

        return $this->get(ImageUtility::class)->getRedirectResponse($object, new ImageParameterBag($width, $height, $request->query->all()));
    }
}
