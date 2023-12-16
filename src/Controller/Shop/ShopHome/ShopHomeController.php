<?php

namespace App\Controller\Shop\ShopHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Form\Shop\ShopRemoveMulti\ShopRemoveMultiForm;
use App\Form\Shop\ShopRemove\ShopRemoveForm;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\SearchBar\SEARCH_TYPE;
use App\Twig\Components\Shop\ShopHome\ShopHomeComponentBuilder;
use Common\Adapter\Endpoints\ShopsEndPoint;
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
    path: '{_locale}/shop/{group_name}/page-{page}-{page_items}',
    name: 'shop_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'page_items' => '\d+',
    ]
)]
class ShopHomeController extends AbstractController
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
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);
        $shopRemoveForm = $this->formFactory->create(new ShopRemoveForm(), $requestDto->request);
        $shopRemoveMultiForm = $this->formFactory->create(new ShopRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        if ($searchBarForm->isSubmitted() && $searchBarForm->isValid()) {
            return $this->controllerUrlRefererRedirect->createRedirectToRoute(
                'shop_home',
                $requestDto->requestReferer->params,
                [],
                [],
                ['searchBar' => [
                    SEARCHBAR_FORM_FIELDS::SEARCH_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_FILTER),
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
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        $shopHomeComponentDto = $this->createShopHomeComponentDto(
            $requestDto,
            $shopCreateForm,
            $shopModifyForm,
            $shopRemoveForm,
            $shopRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarForm->getCsrfToken(),
            $shopsData['shops'],
            $shopsData['pages_total']
        );

        return $this->renderTemplate($shopHomeComponentDto);
    }

    private function getSearchBarFieldsValues(FormInterface $searchBarForm, array $flashBagData): array
    {
        if (!array_key_exists('searchBar', $flashBagData)) {
            return [
                SEARCHBAR_FORM_FIELDS::SEARCH_FILTER => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_FILTER),
                SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $searchBarForm->getFieldData(SEARCHBAR_FORM_FIELDS::SEARCH_VALUE),
            ];
        }

        return [
            SEARCHBAR_FORM_FIELDS::SEARCH_FILTER => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::SEARCH_FILTER],
            SEARCHBAR_FORM_FIELDS::SEARCH_VALUE => $flashBagData['searchBar'][SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
        ];
    }

    private function getShopsData(
        string $groupId,
        string|null $shopNameFilterType,
        string|null $shopNameFilterValue,
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

    private function createShopHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $shopCreateForm,
        FormInterface $shopModifyForm,
        FormInterface $shopRemoveForm,
        FormInterface $shopRemoveMultiForm,
        string|null $searchBarFieldFilter,
        string|null $searchBarFieldValue,
        string $searchBarCsrfToken,
        array $shopsData,
        int $pagesTotal,
    ): HomeSectionComponentDto {
        $shopHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $shopHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new ShopHomeComponentBuilder())
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
            )
            ->validation(
                !empty($shopHomeMessagesError) || !empty($shopHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarFieldFilter,
                $searchBarFieldValue,
                SEARCH_TYPE::SHOP,
                $searchBarCsrfToken,
                ShopsEndPoint::GET_SHOP_DATA,
                $this->generateUrl('shop_home', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ])
            )
            ->shopCreateFormModal(
                $shopCreateForm->getCsrfToken(),
                $this->generateUrl('shop_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->shopRemoveMultiFormModal(
                $shopRemoveMultiForm->getCsrfToken(),
                $this->generateUrl('shop_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->shopRemoveFormModal(
                $shopRemoveForm->getCsrfToken(),
                $this->generateUrl('shop_remove', [
                    'group_name' => $requestDto->groupData->name,
                ])
            )
            ->shopModifyFormModal(
                $shopModifyForm->getCsrfToken(),
                $this->generateUrl('shop_modify', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'shop_name' => '--shop_name--',
                ]),
            )
            ->translationDomainNames(
                'ShopHomeComponent',
                'ShopHomeListComponent',
                'ShopHomeListItemComponent',
            )
            ->build();
    }

    private function renderTemplate(HomeSectionComponentDto $homeSectionComponent): Response
    {
        return $this->render('shop/shop_home/index.html.twig', [
            'homeSectionComponent' => $homeSectionComponent,
        ]);
    }
}
