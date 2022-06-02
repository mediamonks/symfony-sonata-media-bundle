<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use League\Glide\Filesystem\FilesystemException;
use MediaMonks\SonataMediaBundle\ParameterBag\ImageParameterBag;
use MediaMonks\SonataMediaBundle\ParameterBag\MediaParameterBag;
use MediaMonks\SonataMediaBundle\Provider\ProviderPool;
use MediaMonks\SonataMediaBundle\Service\ImageResponseHandler;
use MediaMonks\SonataMediaBundle\Service\MediaResponseHandler;
use Sonata\AdminBundle\Controller\CRUDController as BaseCRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CRUDController extends BaseCRUDController
{
    protected MediaResponseHandler $mediaResponseHandler;
    protected ImageResponseHandler $imageResponseHandler;
    protected ProviderPool $providerPool;
    private array $mediaTemplates;

    public function __construct(
        MediaResponseHandler $mediaResponseHandler,
        ImageResponseHandler $imageResponseHandler,
        ProviderPool $providerPool,
        array $mediaTemplates
    )
    {
        $this->mediaResponseHandler = $mediaResponseHandler;
        $this->imageResponseHandler = $imageResponseHandler;
        $this->providerPool = $providerPool;
        $this->mediaTemplates = $mediaTemplates;
    }

    protected function preCreate(Request $request, object $object): ?Response
    {
        if (!$request->get('provider') && $request->isMethod(Request::METHOD_GET)) {
            return $this->renderWithExtraParams(
                '@MediaMonksSonataMedia/CRUD/select_provider.html.twig',
                [
                    'providers' => $this->getProviders($request),
                    'base_template' => $this->getBaseTemplate(),
                    'admin' => $this->admin,
                    'action' => 'create',
                ]
            );
        }

        return null;
    }

    /**
     * @param Request $request
     * @param string|int $id
     *
     * @return StreamedResponse
     */
    public function downloadAction(Request $request, $id): StreamedResponse
    {
        $media = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $media);

        return $this->mediaResponseHandler->getDownloadResponse($media, new MediaParameterBag($request->query->all()));
    }

    /**
     * @param Request $request
     * @param string|int $id
     * @param int $width
     * @param int $height
     *
     * @return RedirectResponse
     * @throws \League\Flysystem\FilesystemException
     * @throws FilesystemException
     */
    public function imageAction(Request $request, $id, int $width, int $height): RedirectResponse
    {
        $media = $this->admin->getObject($id);

        $this->admin->checkAccess('show', $media);

        return $this->imageResponseHandler->getRedirectResponse($media, new ImageParameterBag($width, $height, $request->query->all()));
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    protected function getProviders(Request $request): array
    {
        $types = $request->query->get('types');
        if (is_array($types)) {
            return $this->providerPool->getProvidersByTypes($types);
        }

        return $this->providerPool->getProviders();
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>
     */
    protected function addRenderExtraParams(array $parameters = []): array
    {
        $parameters = parent::addRenderExtraParams($parameters);
        $parameters['media_templates'] = $this->mediaTemplates;

        return $parameters;
    }
}
