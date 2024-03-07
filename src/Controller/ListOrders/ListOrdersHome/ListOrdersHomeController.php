<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\ListOrdersHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Form\ListOrders\ListOrdersCreate\ListOrdersCreateForm;
use App\Form\ListOrders\ListOrdersModify\ListOrdersModifyForm;
use App\Form\ListOrders\ListOrdersRemoveMulti\ListOrdersRemoveMultiForm;
use App\Form\ListOrders\ListOrdersRemove\ListOrdersRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Twig\Components\ListOrders\ListOrdersHome\Home\ListOrdersHomeSectionComponentDto;
use App\Twig\Components\ListOrders\ListOrdersHome\ListOrdersHomeComponentBuilder;
use Common\Adapter\Endpoints\ListOrdersEndpoints;
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
    path: '{_locale}/{group_name}/list-orders/page-{page}-{page_items}',
    name: 'list_orders_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class ListOrdersHomeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $listOrdersCreateForm = $this->formFactory->create(new ListOrdersCreateForm(), $requestDto->request);
        $listOrdersModifyForm = $this->formFactory->create(new ListOrdersModifyForm(), $requestDto->request);
        $listOrdersRemoveForm = $this->formFactory->create(new ListOrdersRemoveForm(), $requestDto->request);
        $listOrdersRemoveMultiForm = $this->formFactory->create(new ListOrdersRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $listOrdersData = $this->listOrdersGetData(
            $requestDto->groupData->id,
            $searchBarFormFields,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        $listOrdersHomeComponentDto = $this->createListOrdersHomeComponentDto(
            $requestDto,
            $listOrdersCreateForm,
            $listOrdersModifyForm,
            $listOrdersRemoveForm,
            $listOrdersRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarForm->getCsrfToken(),
            $listOrdersData['list_orders'],
            $listOrdersData['pages_total']
        );

        return $this->renderTemplate($listOrdersHomeComponentDto);
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
                'product_home',
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

    private function listOrdersGetData(string $groupId, array $searchBarFormFields, int $page, int $pageItems, string $tokenSession): array
    {
        $listOrdersData = $this->endpoints->listOrdersGetData(
            $groupId,
            [],
            true,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $page,
            $pageItems,
            $tokenSession
        );

        $listOrdersData['data']['list_orders'] = array_map(
            fn (array $listOrdersData) => ListOrdersDataResponse::fromArray($listOrdersData),
            $listOrdersData['data']['list_orders'] ?? []
        );

        return $listOrdersData['data'];
    }

    private function createListOrdersHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $listOrdersCreateForm,
        FormInterface $listOrdersModifyForm,
        FormInterface $listOrdersRemoveForm,
        FormInterface $listOrdersRemoveMultiForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        ?string $searchBarSectionFilterValue,
        string $searchBarCsrfToken,
        array $listOrdersData,
        int $pagesTotal,
    ): ListOrdersHomeSectionComponentDto {
        $shopHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $shopHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new ListOrdersHomeComponentBuilder())
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
                $listOrdersData,
            )
            ->validation(
                !empty($shopHomeMessagesError) || !empty($shopHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                '',
                '',
                '',
                ListOrdersEndpoints::GET_LIST_ORDERS_DATA,
                $this->generateUrl('list_orders_home', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ]),
            )
            ->listOrdersCreateFormModal(
                $listOrdersCreateForm->getCsrfToken(),
                $this->generateUrl('list_orders_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->listOrdersRemoveMultiFormModal(
                $listOrdersRemoveMultiForm->getCsrfToken(),
                $this->generateUrl('list_orders_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->listOrdersRemoveFormModal(
                $listOrdersRemoveForm->getCsrfToken(),
                $this->generateUrl('list_orders_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->listOrdersModifyFormModal(
                $listOrdersModifyForm->getCsrfToken(),
                $this->generateUrl('list_orders_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'list_orders_name' => '--list_orders_name--',
                ]),
            )
            ->build();
    }

    private function renderTemplate(ListOrdersHomeSectionComponentDto $listOrdersHomeSectionComponent): Response
    {
        return $this->render('listOrders/list_orders_home/index.html.twig', [
            'listOrdersHomeSectionComponent' => $listOrdersHomeSectionComponent,
        ]);
    }
}
