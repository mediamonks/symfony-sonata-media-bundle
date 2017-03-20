<?php

namespace MediaMonks\SonataMediaBundle\Controller;

use MediaMonks\SonataMediaBundle\Entity\Media;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class MediaSearchController extends Controller
{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function getAutocompleteItemsAction(Request $request)
    {
        $targetAdmin = $this->get('sonata.admin.pool')->getInstance($request->get('admin_code'));
        if (!$targetAdmin->isGranted('CREATE') && !$targetAdmin->isGranted('EDIT')) {
            throw new AccessDeniedHttpException();
        }

        $mediaAdmin = $this->get('mediamonks.sonata_media.admin.media');
        $mediaAdmin->checkAccess('list');

        $minimumInputLength = 3;
        $searchText = $request->get('q');
        if (mb_strlen($searchText, 'UTF-8') < $minimumInputLength) {
            return $this->json(['status' => 'KO', 'message' => 'Too short search string'], Response::HTTP_FORBIDDEN);
        }

        /*$entityManager = $this->get('doctrine.orm.entity_manager');
        $query = $entityManager->getRepository('MediaMonksSonataMediaBundle:Media')->search($searchText);
        $query->setMaxResults($request->query->getInt('_per_page'));
        $query->setFirstResult(($request->query->getInt('_page') - 1) * $request->query->getInt('_per_page'));*/

        $mediaAdmin->setPersistFilters(false);
        $datagrid = $mediaAdmin->getDatagrid();
        $datagrid->setValue('title', null, $searchText);
        $datagrid->setValue('type', null, 'image');
        $datagrid->setValue('_per_page', null, 100);
        $datagrid->setValue('_page', null, $request->query->get(1, 1));
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
                'label' => $this->get('templating')->render('@MediaMonksSonataMedia/MediaAdmin/autocomplete.html.twig', [
                    'media' => $media
                ])
            ];
        }

        return $this->json([
            'status' => 'OK',
            'more' => false,
            'items' => $items
        ]);
    }
}
