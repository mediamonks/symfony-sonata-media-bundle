<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Templating\EngineInterface;

/**
 * @codeCoverageIgnore
 */
class BatchController
{
    /**
     * @var MediaAdmin
     */
    private $mediaAdmin;

    /**
     * @var EngineInterface
     */
    private $templating;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param MediaAdmin $mediaAdmin
     * @param EngineInterface $templating
     * @param RouterInterface $router
     */
    public function __construct(MediaAdmin $mediaAdmin, EngineInterface $templating, RouterInterface $router)
    {
        $this->mediaAdmin = $mediaAdmin;
        $this->templating = $templating;
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getAutocompleteItemsAction(Request $request)
    {
        $this->mediaAdmin->checkAccess('list');

        $minimumInputLength = 3;
        $searchText = $request->get('q');
        if (mb_strlen($searchText, 'UTF-8') < $minimumInputLength) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Too short search string'], Response::HTTP_FORBIDDEN);
        }

        $this->mediaAdmin->setPersistFilters(false);
        $datagrid = $this->mediaAdmin->getDatagrid();
        $datagrid->setValue('title', null, $searchText);
        $datagrid->setValue('_per_page', null, $request->query->get('_per_page', 1));
        $datagrid->setValue('_page', null, $request->query->get('_page', 10));
        $datagrid->buildPager();

        $pager = $datagrid->getPager();
        $results = $pager->getResults();

        /**
         * @var MediaInterface $media
         */
        $items = [];
        foreach($results as $media) {
            $items[] = [
                'id' => $media->getId(),
                'label' => $this->templating->render('@MediaMonksSonataMedia/CRUD/autocomplete.html.twig', [
                    'media' => $media
                ])
            ];
        }

        return new JsonResponse([
            'status' => 'OK',
            'more' => false,
            'items' => $items
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function listAction(Request $request)
    {
        return new Response($this->templating->render('@MediaMonksSonataMedia/Helper/batch.html.twig'));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadAction(Request $request)
    {
        try {
            /**
             * @var $media AbstractMedia
             */
            $media = $this->mediaAdmin->getNewInstance();
            $media->setProvider('image'); // @todo add support for files
            $media->setBinaryContent(current($request->files->all()));
            // @todo validate
            $this->mediaAdmin->create($media);

            return new JsonResponse([
                'success' => true,
                'id' => $media->getId(),
                'title' => $media->getTitle(),
                'type' => $media->getType(),
                'provider' => $media->getProvider(),
                'editUrl' => $this->router->generate('admin_mediamonks_sonatamedia_media_edit', [
                    'id' => $media->getId()
                ])
            ]);
        }
        catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function createByBatchReferencesAction(Request $request)
    {
        return new JsonResponse([]);
    }
}
