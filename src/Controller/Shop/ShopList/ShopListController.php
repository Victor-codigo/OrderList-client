<?php

namespace App\Controller\Shop\ShopList;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route(
    path: '{_locale}/shop/{group_name}/shop-searchbar-autocomplete',
    name: 'shop_searchbar_autocomplete',
    methods: ['GET'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopListController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private Environment $twig
    ) {
    }

    public function __invoke(RequestDto $requestDto): JsonResponse
    {
        $shopsData = $this->getShopsData(
            $requestDto->groupData->id,
            $requestDto->request->query->get('shop_name_filter_type'),
            $requestDto->request->query->get('shop_name_filter_value'),
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        return $this->createResponse(
            $requestDto->request->query->get('shop_name_filter_type', ''),
            $shopsData
        );
    }

    /**
     * @return array<{
     *  pages: int,
     *  pages_total: int,
     *  shops: ShopDataResponse[],
     * }>
     */
    private function getShopsData(string $groupId, string|null $shopNameFilterType, string|null $shopNameFilterValue, int $page, int $pageItems, string $tokenSession): array
    {
        if (null === $shopNameFilterValue || '' === $shopNameFilterValue) {
            return [
                'pages' => 1,
                'pages_total' => 0,
                'shops' => [],
            ];
        }

        $shopsData = $this->endpoints->shopsGetData(
            $groupId,
            null,
            null,
            null,
            $shopNameFilterType,
            $shopNameFilterValue,
            $page,
            $pageItems,
            true,
            $tokenSession
        );

        $shopsData['data']['shops'] = array_map(
            fn (array $shopData) => ShopDataResponse::fromArray($shopData),
            $shopsData['data']['shops']
        );

        return $shopsData['data'];
    }

    private function createResponse(string $searchFilter, array $shopsData): JsonResponse
    {
        return new JsonResponse([
            SEARCHBAR_FORM_FIELDS::SEARCH_FILTER => $searchFilter,
            SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => array_map(
                fn (ShopDataResponse $shopData) => $shopData->name,
                $shopsData['shops']
            ),
        ]);
    }
}
