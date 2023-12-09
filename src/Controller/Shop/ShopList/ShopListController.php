<?php

namespace App\Controller\Shop\ShopList;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Form\Shop\ShopRemove\ShopRemoveForm;
use App\Twig\Components\Shop\ShopList\List\ShopListComponentDto;
use App\Twig\Components\Shop\ShopList\ShopListComponentBuilder;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

#[Route(
    path: '{_locale}/shop/{group_name}/shop-list/page-{page}-{page_items}',
    name: 'shop_list',
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

    public function __invoke(RequestDto $requestDto): Response
    {
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);
        $shopRemoveForm = $this->formFactory->create(new ShopRemoveForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $shopsData = $this->getShopsData(
            $requestDto->groupData->id,
            $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_FILTER),
            $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        $shopListComponentDto = $this->createShopListComponentDto(
            $shopModifyForm,
            $shopRemoveForm,
            $requestDto,
            $shopsData['shops'],
            $shopsData['pages_total'],
        );

        return $this->createResponse($shopListComponentDto);
    }

    private function createShopListComponentDto(FormInterface $shopModifyForm, FormInterface $shopRemoveForm, RequestDto $requestDto, array $shopsData, int $pagesTotal): ShopListComponentDto
    {
        return (new ShopListComponentBuilder())
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->shopModifyForm(
                $shopModifyForm->getCsrfToken(),
                $this->generateUrl('shop_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'shop_name' => '--shop_name--',
                ])
            )
            ->shopRemoveForm(
                $shopRemoveForm->getCsrfToken(),
                $this->generateUrl('shop_remove', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )

            ->shops(
                $shopsData,
                Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            )
            ->validation(
                [],
                false
            )
            ->build();
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

    private function createResponse(ShopListComponentDto $shopListComponentDto): Response
    {
        $template = $this->twig
            ->createTemplate("{{ component('ShopListComponent', { data: shopListComponentDto }) }}")
            ->render(['shopListComponentDto' => $shopListComponentDto]);

        return new Response($template);
    }
}
