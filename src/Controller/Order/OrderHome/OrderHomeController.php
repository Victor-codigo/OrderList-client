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
use App\Twig\Components\Order\OrderHome\Home\OrderHomeSectionComponentDto;
use App\Twig\Components\Order\OrderHome\OrderHomeComponentBuilder;
use Common\Adapter\Endpoints\OrdersEndpoint;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\ControllerUrlRefererRedirect\FLASH_BAG_TYPE_SUFFIX;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/{list_orders_name}/orders/page-{page}-{page_items}',
    name: 'order_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class OrderHomeController extends AbstractController
{
    private const ORDER_NAME_PLACEHOLDER = '--order_name--';

    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect
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
            $requestDto->listOrdersData->id,
            $searchBarFormFields,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
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

        return $this->renderTemplate($orderHomeComponentDto);
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
                'order_home',
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
            ->validation(
                !empty($orderHomeMessagesError) || !empty($orderHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                OrdersEndpoint::GET_ORDERS_DATA,
                $this->generateUrl('order_home', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'list_orders_name' => $requestDto->listOrdersData->name,
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ]),
            )
            ->orderCreateFormModal(
                $orderCreateForm->getCsrfToken(),
                null,
                $this->generateUrl('order_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
                $requestDto->groupData->id,
                $requestDto->listOrdersData->id
            )
            ->orderRemoveMultiFormModal(
                $orderRemoveMultiForm->getCsrfToken(),
                $this->generateUrl('order_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->orderRemoveFormModal(
                $orderRemoveForm->getCsrfToken(),
                $this->generateUrl('order_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->orderModifyFormModal(
                $orderModifyForm->getCsrfToken(),
                $this->generateUrl('order_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'order_name' => self::ORDER_NAME_PLACEHOLDER,
                ]),
                $requestDto->groupData->id,
                $requestDto->listOrdersData->id
            )
            ->productsListModal(
                $requestDto->groupData->id,
                Config::API_IMAGES_PRODUCTS_PATH,
                Config::PRODUCT_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
            )
            ->build();
    }

    private function renderTemplate(OrderHomeSectionComponentDto $orderHomeSectionComponent): Response
    {
        return $this->render('order/order_home/index.html.twig', [
            'OrderHomeSectionComponent' => $orderHomeSectionComponent,
        ]);
    }
}
