<?php

declare(strict_types=1);

namespace App\Controller\Product\ProductHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Product\ProductCreate\ProductCreateForm;
use App\Form\Product\ProductModify\ProductModifyForm;
use App\Form\Product\ProductRemoveMulti\ProductRemoveMultiForm;
use App\Form\Product\ProductRemove\ProductRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\Product\ProductHome\Home\ProductHomeSectionComponentDto;
use App\Twig\Components\Product\ProductHome\ProductHomeComponentBuilder;
use Common\Adapter\Endpoints\ProductsEndPoint;
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
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/{section}/page-{page}-{page_items}',
    name: 'product_home_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'section' => 'product',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
#[Route(
    path: '{_locale}/{section}/page-{page}-{page_items}',
    name: 'product_home_no_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'section' => 'product',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class ProductHomeController extends AbstractController
{
    private const PRODUCT_NAME_PLACEHOLDER = '--product_name--';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GetPageTitleService $getPageTitleService,
        private RouterSelector $routerSelector,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $productCreateForm = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);
        $productModifyForm = $this->formFactory->create(new ProductModifyForm(), $requestDto->request);
        $productRemoveForm = $this->formFactory->create(new ProductRemoveForm(), $requestDto->request);
        $productRemoveMultiForm = $this->formFactory->create(new ProductRemoveMultiForm(), $requestDto->request);
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $productsData = $this->getProductsData(
            $requestDto->groupData->id,
            $searchBarFormFields,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->getTokenSessionOrFail()
        );

        $shopsData = $this->getProductsShopsData($requestDto->groupData->id, $productsData['products'], $requestDto->getTokenSessionOrFail());
        $productsShopsPricesData = $this->getProductsShopPrices($requestDto->groupData->id, $productsData['products'], $shopsData, $requestDto->getTokenSessionOrFail());

        $productHomeComponentDto = $this->createProductHomeComponentDto(
            $requestDto,
            $productCreateForm,
            $productModifyForm,
            $productRemoveForm,
            $productRemoveMultiForm,
            $shopCreateForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarForm->getCsrfToken(),
            $productsData['products'],
            $shopsData,
            $productsShopsPricesData,
            $productsData['pages_total']
        );

        return $this->renderTemplate($productHomeComponentDto);
    }

    /**
     * @return array<{
     *      SEARCHBAR_FORM_FIELDS::SECTION_FILTER: string,
     *      SEARCHBAR_FORM_FIELDS::NAME_FILTER: string,
     *      SEARCHBAR_FORM_FIELDS::SEARCH_VALUE: string
     * }>
     */
    private function getSearchBarFieldsValues(FormInterface $searchBarForm, array $flashBagData): array
    {
        if (!array_key_exists('searchBar', $flashBagData)) {
            return [
                SEARCHBAR_FORM_FIELDS::SECTION_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SECTION_FILTER),
                SEARCHBAR_FORM_FIELDS::NAME_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::NAME_FILTER),
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
            ];
        }

        return [
            SEARCHBAR_FORM_FIELDS::SECTION_FILTER => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            SEARCHBAR_FORM_FIELDS::NAME_FILTER => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
        ];
    }

    private function searchBarForm(FormInterface $searchBarForm, RequestDto $requestDto): ?RedirectResponse
    {
        if ($searchBarForm->isSubmitted() && $searchBarForm->isValid()) {
            return $this->controllerUrlRefererRedirect->createRedirectToRoute(
                $this->routerSelector->getRouteNameWithSuffix('product_home'),
                $requestDto->requestReferer->params,
                [],
                [],
                ['searchBar' => [
                    SEARCHBAR_FORM_FIELDS::SECTION_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SECTION_FILTER),
                    SEARCHBAR_FORM_FIELDS::NAME_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::NAME_FILTER),
                    SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
                ]]
            );
        }

        return null;
    }

    private function getProductsData(string $groupId, array $searchBarFormFields, int $page, int $pageItems, string $tokenSession): array
    {
        $productNameFilterType = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER];
        $productNameFilterValue = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE];
        $shopNameFilterType = null;
        $shopNameFilterValue = null;

        if (SECTION_FILTERS::SHOP->value === $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER]) {
            $productNameFilterType = null;
            $productNameFilterValue = null;
            $shopNameFilterType = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER];
            $shopNameFilterValue = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE];
        }

        $productsData = $this->endpoints->productGetData(
            $groupId,
            null,
            null,
            null,
            $productNameFilterType,
            $productNameFilterValue,
            $shopNameFilterType,
            $shopNameFilterValue,
            $page,
            $pageItems,
            true,
            $tokenSession
        );

        $productsData['data']['products'] = array_map(
            fn (array $productData) => ProductDataResponse::fromArray($productData),
            $productsData['data']['products']
        );

        return $productsData['data'];
    }

    private function getProductsShopsData(string $groupId, array $productsData, string $tokenSession): array
    {
        $productsId = array_column($productsData, 'id');

        if (empty($productsId)) {
            return [];
        }

        $shopsData = $this->endpoints->shopsGetData(
            $groupId,
            null,
            $productsId,
            null,
            null,
            null,
            1,
            Config::PAGINATION_ITEMS_MAX,
            true,
            $tokenSession
        );

        return array_map(
            fn (array $shopData) => ShopDataResponse::fromArray($shopData),
            $shopsData['data']['shops']
        );
    }

    private function getProductsShopPrices(string $groupId, array $productsData, array $shopsData, string $tokenSession): array
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

    private function createProductHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $productCreateForm,
        FormInterface $productModifyForm,
        FormInterface $productRemoveForm,
        FormInterface $productRemoveMultiForm,
        FormInterface $shopCreateForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        ?string $searchBarSectionFilterValue,
        string $searchBarCsrfToken,
        array $productsData,
        array $shopsData,
        array $productsShopsPriceData,
        int $pagesTotal,
    ): ProductHomeSectionComponentDto {
        $productHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $productHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new ProductHomeComponentBuilder())
            ->title(
                null,
                'user' === $requestDto->groupData->type ? null : $requestDto->groupData->name
            )
            ->errors(
                $productHomeMessagesOk,
                $productHomeMessagesError
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $productsData,
                $shopsData,
                $productsShopsPriceData
            )
            ->validation(
                !empty($productHomeMessagesError) || !empty($productHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                ProductsEndPoint::GET_PRODUCT_DATA,
                $this->routerSelector->generateRouteWithDefaults('product_home', [])
            )
            ->productCreateFormModal(
                $productCreateForm->getCsrfToken(),
                null,
                $this->routerSelector->generateRoute('product_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->productRemoveMultiFormModal(
                $productRemoveMultiForm->getCsrfToken(),
                $this->routerSelector->generateRoute('product_remove', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )
            ->productRemoveFormModal(
                $productRemoveForm->getCsrfToken(),
                $this->routerSelector->generateRoute('product_remove', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ])
            )
            ->productModifyFormModal(
                $productModifyForm->getCsrfToken(),
                $this->routerSelector->generateRoute('product_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'product_name' => self::PRODUCT_NAME_PLACEHOLDER,
                ]),
            )
            ->shopsListModal(
                $requestDto->groupData->id,
                Config::API_IMAGES_SHOP_PATH,
                Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            )
            ->shopCreateModal(
                $requestDto->groupData->id,
                $shopCreateForm->getCsrfToken(),
                $this->routerSelector->generateRoute('shop_create_ajax', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->build();
    }

    private function renderTemplate(ProductHomeSectionComponentDto $productHomeSectionComponent): Response
    {
        return $this->render('product/product_home/index.html.twig', [
            'ProductHomeSectionComponent' => $productHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('ProductHomeComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
