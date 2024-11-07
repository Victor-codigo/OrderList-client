<?php

declare(strict_types=1);

namespace App\Controller\Share\ShareListOrders;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\OrderDataResponse;
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
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route(
    path: '{_locale}/share/list-orders/{shared_recourse_id}/page-{page}-{page_items}',
    name: 'share_list_orders',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'shared_recourse_id' => '('.Requirement::UUID_V4.'|--shared_recourse_id--)',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class ShareListOrdersController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private RouterSelector $routerSelector,
        private GetPageTitleService $getPageTitleService,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private TranslatorInterface $translator,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $sharedRecourseId = $requestDto->request->attributes->get('shared_recourse_id');
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        [
            'pages_total' => $pagesTotal,
            'list_orders' => $listOrdersData,
            'orders' => $ordersData,
        ] = $this->getSharedListOrdersGetData(
            $sharedRecourseId,
            $requestDto->page,
            $requestDto->pageItems,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE]
        );

        if (!$listOrdersData instanceof ListOrdersDataResponse) {
            return $this->renderTemplateListOrdersSharedNotFound();
        }

        $shareListOrdersHomeComponentDto = $this->createOrderHomeComponentDto(
            $requestDto,
            $sharedRecourseId,
            $listOrdersData,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarForm->getCsrfToken(),
            $ordersData,
            $pagesTotal
        );

        return $this->renderTemplate($shareListOrdersHomeComponentDto, $listOrdersData->name);
    }

    /**
     * @return array{
     *  page: int,
     *  pages_total: int,
     *  list_orders: ListOrdersDataResponse,
     *  orders: OrderDataResponse[]
     * }
     */
    private function getSharedListOrdersGetData(string $sharedListOrdersId, int $page, int $pageItems, ?string $filterText, ?string $filterValue): array
    {
        $sharedListOrdersData = $this->endpoints->sharedListOrdersGetData($sharedListOrdersId, $page, $pageItems, $filterText, $filterValue);

        if (!empty($sharedListOrdersData['errors'])) {
            return [
                'page' => 1,
                'pages_total' => 1,
                'list_orders' => [],
                'orders' => [],
            ];
        }

        $sharedListOrdersData['data']['list_orders'] = ListOrdersDataResponse::fromArray($sharedListOrdersData['data']['list_orders']);
        $sharedListOrdersData['data']['orders'] = array_map(
            fn (array $orderData) => OrderDataResponse::fromArray($orderData),
            $sharedListOrdersData['data']['orders']
        );

        return [
            'page' => $sharedListOrdersData['data']['page'],
            'pages_total' => $sharedListOrdersData['data']['pages_total'],
            'list_orders' => $sharedListOrdersData['data']['list_orders'],
            'orders' => $sharedListOrdersData['data']['orders'],
        ];
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
                'share_list_orders',
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

    /**
     * @param OrderDataResponse[] $ordersData
     */
    private function createOrderHomeComponentDto(
        RequestDto $requestDto,
        string $sharedRecourseId,
        ListOrdersDataResponse $listOrdersData,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        string $searchBarCsrfToken,
        array $ordersData,
        int $pagesTotal,
    ): OrderHomeSectionComponentDto {
        return (new OrderHomeComponentBuilder())
            ->title(
                $listOrdersData->name,
                null
            )
            ->homeSection(
                false,
                true,
                true
            )
            ->listOrders(
                $listOrdersData->id,
                $listOrdersData->groupId
            )
            ->shareButton(
                ''
            )
            ->errors(
                [],
                []
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $ordersData
            )
            ->validation(
                false,
            )
            ->searchBar(
                $listOrdersData->id,
                [SECTION_FILTERS::ORDER],
                $searchBarSearchValue,
                null,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                OrdersEndpoint::GET_ORDERS_DATA,
                $this->routerSelector->generateRouteWithDefaults('share_list_orders', [
                    'shared_recourse_id' => $sharedRecourseId,
                ])
            )
            ->orderCreateFormModal(
                '',
                null,
                '',
                '',
                ''
            )
            ->orderRemoveMultiFormModal(
                '',
                ''
            )
            ->orderRemoveFormModal(
                '',
                ''
            )
            ->orderModifyFormModal(
                '',
                '',
                '',
                ''
            )
            ->productsListModal(
                '',
                '',
                ''
            )
            ->build();
    }

    private function renderTemplate(OrderHomeSectionComponentDto $orderHomeSectionComponent, string $listName): Response
    {
        return $this->render('share/share_not_found/index.html.twig', [
            'OrderHomeSectionComponent' => $orderHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->setTitleWithDomainName($listName),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
            'listOrdersNotFound' => false,
        ]);
    }

    private function renderTemplateListOrdersSharedNotFound(): Response
    {
        return $this->render('share/share_not_found/index.html.twig', [
            'pageTitle' => $this->getPageTitleService->setTitleWithDomainName($this->translator->trans('share.page.title', [], 'ShareNotFoundComponent')),            'listOrdersNotFound' => true,
        ]);
    }
}
