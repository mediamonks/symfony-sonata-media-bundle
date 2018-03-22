<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Admin\MediaAdmin;
use MediaMonks\SonataMediaBundle\Model\MediaInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;

class HelperController
{
    const QUERY_MINIMUM_LENGTH = 3;

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
     * @return JsonResponse
     */
    public function getAutocompleteItemsAction(Request $request): JsonResponse
    {
        $this->mediaAdmin->checkAccess('list');

        if (mb_strlen($request->get('q'), 'UTF-8') < self::QUERY_MINIMUM_LENGTH) {
            return new JsonResponse(['status' => 'KO', 'message' => 'Search string too short'], Response::HTTP_FORBIDDEN);
        }

        return new JsonResponse([
            'status' => 'OK',
            'more' => false,
            'items' => $this->transformResults($this->getPagerResults($request))
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    protected function getPagerResults(Request $request): array
    {
        $this->mediaAdmin->setPersistFilters(false);

        $datagrid = $this->mediaAdmin->getDatagrid();
        $datagrid->setValue('title', null, $request->get('q'));
        $datagrid->setValue('_per_page', null, $request->query->get('_per_page', 10));
        $datagrid->setValue('_page', null, $request->query->get('_page', 1));
        if ($request->query->has('type')) {
            $datagrid->setValue('type', null, $request->query->get('type'));
        }
        if ($request->query->has('provider')) {
            $datagrid->setValue('provider', null, $request->query->get('provider'));
        }

        $datagrid->buildPager();

        $pager = $datagrid->getPager();
        return $pager->getResults();
    }

    /**
     * @param MediaInterface[] $results
     * @return array
     */
    protected function transformResults(array $results): array
    {
        $items = [];
        foreach($results as $media) {
            $items[] = [
                'id' => $media->getId(),
                'label' => $this->templating->render('@MediaMonksSonataMedia/CRUD/autocomplete.html.twig', [
                    'media' => $media
                ])
            ];
        }

        return $items;
    }
}
