<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\ListOrdersHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Form\ListOrders\ListOrdersCreate\ListOrdersCreateForm;
use App\Form\ListOrders\ListOrdersModify\ListOrdersModifyForm;
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
        $listOrdersData = $this->listOrdersGetData(
            $requestDto->groupData->id,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        $listOrdersHomeComponentDto = $this->createListOrdersHomeComponentDto(
            $requestDto,
            $listOrdersCreateForm,
            $listOrdersModifyForm,
            $listOrdersData['list_orders'],
            $listOrdersData['pages_total']
        );

        return $this->renderTemplate($listOrdersHomeComponentDto);
    }

    private function listOrdersGetData(string $groupId, int $page, int $pageItems, string $tokenSession): array
    {
        $listOrdersData = $this->endpoints->listOrdersGetData(
            $groupId,
            [],
            true,
            null,
            null,
            null,
            $page,
            $pageItems,
            $tokenSession
        );

        $listOrdersData['data']['list_orders'] = array_map(
            fn (array $listOrdersData) => ListOrdersDataResponse::fromArray($listOrdersData),
            $listOrdersData['data']['list_orders']
        );

        return $listOrdersData['data'];
    }

    private function createListOrdersHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $listOrdersCreateForm,
        FormInterface $listOrdersModifyForm,
        // FormInterface $shopRemoveForm,
        // FormInterface $shopRemoveMultiForm,
        // FormInterface $productCreateForm,
        // string|null $searchBarSearchValue,
        // string|null $searchBarNameFilterValue,
        // string $searchBarCsrfToken,
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
                // $searchBarSearchValue,
                // $searchBarNameFilterValue,
                // $searchBarCsrfToken,
                '',
                '',
                '',
                ListOrdersEndpoints::GET_LIST_ORDERS_DATA,
                $this->generateUrl('shop_home', [
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
                $listOrdersCreateForm->getCsrfToken(),
                $this->generateUrl('shop_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->listOrdersRemoveFormModal(
                $listOrdersCreateForm->getCsrfToken(),
                $this->generateUrl('shop_remove', [
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
