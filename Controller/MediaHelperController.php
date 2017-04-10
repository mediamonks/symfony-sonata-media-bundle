<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Model\AbstractMedia;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class MediaHelperController
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
     * @param MediaAdmin $mediaAdmin
     * @param EngineInterface $templating
     */
    public function __construct(MediaAdmin $mediaAdmin, EngineInterface $templating)
    {
        $this->mediaAdmin = $mediaAdmin;
        $this->templating = $templating;
    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
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
         * @var Media $media
         */
        $items = [];
        foreach($results as $media) {
            $items[] = [
                'id' => $media->getId(),
                'label' => $this->templating->render('@MediaMonksSonataMedia/MediaAdmin/autocomplete.html.twig', [
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
}
