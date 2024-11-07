<?php

declare(strict_types=1);

namespace App\Controller\Order\OrderHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\OrderDataResponse;
use App\Form\Order\OrderCreate\OrderCreateForm;
use App\Form\Order\OrderModify\OrderModifyForm;
use App\Form\Order\OrderRemoveMulti\OrderRemoveMultiForm;
use App\Form\Order\OrderRemove\OrderRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use App\Twig\Components\Order\OrderHome\Home\OrderHomeSectionComponentDto;
use App\Twig\Components\Order\OrderHome\OrderHomeComponentBuilder;
use Common\Adapter\Endpoints\OrdersEndpoint;
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
    path: '{_locale}/{group_name}/{list_orders_name}/{section}/page-{page}-{page_items}',
    name: 'order_home_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'page' => '\d+',
        'page_items' => '\d+',
        'section' => 'orders',
    ]
)]
#[Route(
    path: '{_locale}/{list_orders_name}/{section}/page-{page}-{page_items}',
    name: 'order_home_no_group',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'page' => '\d+',
        'page_items' => '\d+',
        'section' => 'orders',
    ]
)]
class OrderHomeController extends AbstractController
{
    private const ORDER_NAME_PLACEHOLDER = '--order_name--';

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
        $orderCreateForm = $this->formFactory->create(new OrderCreateForm(), $requestDto->request);
        $orderModifyForm = $this->formFactory->create(new OrderModifyForm(), $requestDto->request);
        $orderRemoveForm = $this->formFactory->create(new OrderRemoveForm(), $requestDto->request);
        $orderRemoveMultiForm = $this->formFactory->create(new OrderRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $ordersData = $this->getOrdersData(
            $requestDto->groupData->id,
            $requestDto->getListOrdersData()->id,
            $searchBarFormFields,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->getTokenSessionOrFail()
        );

        $orderHomeComponentDto = $this->createOrderHomeComponentDto(
            $requestDto,
            $orderCreateForm,
            $orderModifyForm,
            $orderRemoveForm,
            $orderRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarForm->getCsrfToken(),
            $ordersData['orders'],
            $ordersData['pages_total']
        );

        return $this->renderTemplate($orderHomeComponentDto, $requestDto->getListOrdersData()->name);
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
        if (null === $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE)) {
            return [
                SEARCHBAR_FORM_FIELDS::SECTION_FILTER => null,
                SEARCHBAR_FORM_FIELDS::NAME_FILTER => null,
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => null,
            ];
        }

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
                $this->routerSelector->getRouteNameWithSuffix('order_home'),
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

    private function getOrdersData(string $groupId, string $listOrdersId, array $searchBarFormFields, int $page, int $pageItems, string $tokenSession): array
    {
        $ordersData = $this->endpoints->ordersGetData(
            $groupId,
            null,
            $listOrdersId,
            $page,
            $pageItems,
            true,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $tokenSession
        );

        if (!empty($ordersData['errors'])) {
            return [
                'page' => 1,
                'pages_total' => 1,
                'orders' => [],
            ];
        }

        $ordersData['data']['orders'] = array_map(
            fn (array $orderData) => OrderDataResponse::fromArray($orderData),
            $ordersData['data']['orders']
        );

        return $ordersData['data'];
    }

    /**
     * @param OrderDataResponse[] $ordersData
     */
    private function createOrderHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $orderCreateForm,
        FormInterface $orderModifyForm,
        FormInterface $orderRemoveForm,
        FormInterface $orderRemoveMultiForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        ?string $searchBarSectionFilterValue,
        string $searchBarCsrfToken,
        array $ordersData,
        int $pagesTotal,
    ): OrderHomeSectionComponentDto {
        $orderHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $orderHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new OrderHomeComponentBuilder())
            ->title(
                $requestDto->getListOrdersData()->name,
                'user' === $requestDto->groupData->type ? null : $requestDto->groupData->name
            )
            ->homeSection(
                true,
                false,
                false
            )
            ->listOrders(
                $requestDto->getListOrdersData()->id,
                $requestDto->groupData->id
            )
            ->errors(
                $orderHomeMessagesOk,
                $orderHomeMessagesError
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $ordersData
            )
            ->shareButton(
                $this->routerSelector->generateRoute('share_list_orders', [
                    'shared_recourse_id' => '--shared_recourse_id--',
                    'page' => 1,
                    'page_items' => 20,
                ])
            )
            ->validation(
                !empty($orderHomeMessagesError) || !empty($orderHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                [SECTION_FILTERS::ORDER, SECTION_FILTERS::PRODUCT, SECTION_FILTERS::SHOP],
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                OrdersEndpoint::GET_ORDERS_DATA,
                $this->routerSelector->generateRouteWithDefaults('order_home', [
                    'list_orders_name' => $requestDto->listOrdersUrlEncoded,
                ])
            )
            ->orderCreateFormModal(
                $orderCreateForm->getCsrfToken(),
                null,
                $this->routerSelector->generateRoute('order_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
                $requestDto->groupData->id,
                $requestDto->getListOrdersData()->id
            )
            ->orderRemoveMultiFormModal(
                $orderRemoveMultiForm->getCsrfToken(),
                $this->routerSelector->generateRoute('order_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->orderRemoveFormModal(
                $orderRemoveForm->getCsrfToken(),
                $this->routerSelector->generateRoute('order_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->orderModifyFormModal(
                $orderModifyForm->getCsrfToken(),
                $this->routerSelector->generateRoute('order_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'order_name' => self::ORDER_NAME_PLACEHOLDER,
                ]),
                $requestDto->groupData->id,
                $requestDto->getListOrdersData()->id
            )
            ->productsListModal(
                $requestDto->groupData->id,
                Config::API_IMAGES_PRODUCTS_PATH,
                Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            )
            ->build();
    }

    private function renderTemplate(OrderHomeSectionComponentDto $orderHomeSectionComponent, string $listName): Response
    {
        return $this->render('order/order_home/index.html.twig', [
            'OrderHomeSectionComponent' => $orderHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->setTitleWithDomainName($listName),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
