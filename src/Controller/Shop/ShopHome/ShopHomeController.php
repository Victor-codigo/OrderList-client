<?php

namespace App\Controller\Shop\ShopHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Product\ProductCreate\ProductCreateForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Form\Shop\ShopRemoveMulti\ShopRemoveMultiForm;
use App\Form\Shop\ShopRemove\ShopRemoveForm;
use App\Twig\Components\Shop\ShopHome\Home\ShopHomeSectionComponentDto;
use App\Twig\Components\Shop\ShopHome\ShopHomeComponentBuilder;
use Common\Adapter\Endpoints\ShopsEndPoint;
use Common\Adapter\Router\RouterSelector;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\PageTitle\GetPageTitleService;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/{section}/page-{page}-{page_items}',
    name: 'shop_home_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'section' => 'shop',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
#[Route(
    path: '{_locale}/{section}/page-{page}-{page_items}',
    name: 'shop_home_no_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'section' => 'shop',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class ShopHomeController extends AbstractController
{
    private const SHOP_NAME_PLACEHOLDER = '--shop_name--';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GetPageTitleService $getPageTitleService,
        private RouterSelector $routerSelector
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);
        $shopRemoveForm = $this->formFactory->create(new ShopRemoveForm(), $requestDto->request);
        $shopRemoveMultiForm = $this->formFactory->create(new ShopRemoveMultiForm(), $requestDto->request);
        $productCreateFrom = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        if ($searchBarForm->isSubmitted() && $searchBarForm->isValid()) {
            return $this->controllerUrlRefererRedirect->createRedirectToRoute(
                $this->routerSelector->getRouteNameWithSuffix('shop_home'),
                $requestDto->requestReferer->params,
                [],
                [],
                ['searchBar' => [
                    SEARCHBAR_FORM_FIELDS::NAME_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::NAME_FILTER),
                    SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
                ]]
            );
        }

        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $shopsData = $this->getShopsData(
            $requestDto->groupData->id,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->getTokenSessionOrFail()
        );

        $productsData = $this->getShopsProductsData($requestDto->groupData->id, $shopsData['shops'], $requestDto->getTokenSessionOrFail());
        $productsShopsPricesData = $this->getProductsShopPrices($requestDto->groupData->id, $shopsData['shops'], $productsData, $requestDto->getTokenSessionOrFail());

        $shopHomeComponentDto = $this->createShopHomeComponentDto(
            $requestDto,
            $shopCreateForm,
            $shopModifyForm,
            $shopRemoveForm,
            $shopRemoveMultiForm,
            $productCreateFrom,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarForm->getCsrfToken(),
            $shopsData['shops'],
            $productsData,
            $productsShopsPricesData,
            $shopsData['pages_total']
        );

        return $this->renderTemplate($shopHomeComponentDto);
    }

    private function getSearchBarFieldsValues(FormInterface $searchBarForm, array $flashBagData): array
    {
        if (!array_key_exists('searchBar', $flashBagData)) {
            return [
                SEARCHBAR_FORM_FIELDS::NAME_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::NAME_FILTER),
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
            ];
        }

        return [
            SEARCHBAR_FORM_FIELDS::NAME_FILTER => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
        ];
    }

    private function getShopsData(
        string $groupId,
        ?string $shopNameFilterType,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        string $tokenSession
    ): array {
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

    private function getShopsProductsData(string $groupId, array $shopData, string $tokenSession): array
    {
        $shopsId = array_column($shopData, 'id');

        if (empty($shopsId)) {
            return [];
        }

        $productsData = $this->endpoints->productGetData(
            $groupId,
            null,
            $shopsId,
            null,
            null,
            null,
            null,
            null,
            1,
            100,
            true,
            $tokenSession
        );

        return array_map(
            fn (array $productData) => ProductDataResponse::fromArray($productData),
            $productsData['data']['products']
        );
    }

    private function getProductsShopPrices(string $groupId, array $shopsData, array $productsData, string $tokenSession): array
    {
        $productsId = array_column($productsData, 'id');
        $shopsId = array_column($shopsData, 'id');

        if (empty($productsId) || empty($shopsId)) {
            return [];
        }

        $productsShopPriceData = $this->endpoints->getProductShopPrice($groupId, $productsId, $shopsId, $tokenSession);

        return array_map(
            fn (array $productShopPriceData) => ProductShopPriceDataResponse::fromArray($productShopPriceData),
            $productsShopPriceData['data']['products_shops']
        );
    }

    private function createShopHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $shopCreateForm,
        FormInterface $shopModifyForm,
        FormInterface $shopRemoveForm,
        FormInterface $shopRemoveMultiForm,
        FormInterface $productCreateForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        string $searchBarCsrfToken,
        array $shopsData,
        array $productsData,
        array $productsShopsPriceData,
        int $pagesTotal,
    ): ShopHomeSectionComponentDto {
        $shopHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $shopHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new ShopHomeComponentBuilder())
            ->title(
                null
            )
            ->errors(
                $shopHomeMessagesOk,
                $shopHomeMessagesError
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $shopsData,
                $productsData,
                $productsShopsPriceData
            )
            ->validation(
                !empty($shopHomeMessagesError) || !empty($shopHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarSearchValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                ShopsEndPoint::GET_SHOP_DATA,
                $this->routerSelector->generateRouteWithDefaults('shop_home', [])
            )
            ->shopCreateFormModal(
                $shopCreateForm->getCsrfToken(),
                $this->routerSelector->generateRoute('shop_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->shopRemoveMultiFormModal(
                $shopRemoveMultiForm->getCsrfToken(),
                $this->routerSelector->generateRoute('shop_remove', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )
            ->shopRemoveFormModal(
                $shopRemoveForm->getCsrfToken(),
                $this->routerSelector->generateRoute('shop_remove', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )
            ->shopModifyFormModal(
                $shopModifyForm->getCsrfToken(),
                $this->routerSelector->generateRoute('shop_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'shop_name' => self::SHOP_NAME_PLACEHOLDER,
                ]),
            )
            ->productsListModal(
                $requestDto->groupData->id,
                Config::API_IMAGES_PRODUCTS_PATH,
                Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            )
            ->productCreateModal(
                $requestDto->groupData->id,
                $productCreateForm->getCsrfToken(),
                $this->generateUrl('product_create_ajax', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )
            ->build();
    }

    private function renderTemplate(ShopHomeSectionComponentDto $shopHomeSectionComponent): Response
    {
        return $this->render('shop/shop_home/index.html.twig', [
            'shopHomeSectionComponent' => $shopHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('ShopHomeComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
