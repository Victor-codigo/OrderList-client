<?php

declare(strict_types=1);

namespace App\Controller\Product\ProductHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ProductDataResponse;
use App\Form\Product\ProductCreate\ProductCreateForm;
use App\Form\Product\ProductModify\ProductModifyForm;
use App\Form\Product\ProductRemoveMulti\ProductRemoveMultiForm;
use App\Form\Product\ProductRemove\ProductRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Twig\Components\HomeSection\Home\HomeSectionComponentDto;
use App\Twig\Components\Product\ProductHome\ProductHomeComponentBuilder;
use App\Twig\Components\SearchBar\SEARCH_TYPE;
use Common\Adapter\Endpoints\ProductsEndPoint;
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
    path: '{_locale}/{group_name}/product/page-{page}-{page_items}',
    name: 'product_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
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
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $productCreateForm = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);
        $productModifyForm = $this->formFactory->create(new ProductModifyForm(), $requestDto->request);
        $productRemoveForm = $this->formFactory->create(new ProductRemoveForm(), $requestDto->request);
        $productRemoveMultiForm = $this->formFactory->create(new ProductRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        if ($searchBarForm->isSubmitted() && $searchBarForm->isValid()) {
            return $this->controllerUrlRefererRedirect->createRedirectToRoute(
                'product_home',
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

        $productsData = $this->getProductsData(
            $requestDto->groupData->id,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            null,
            null,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->tokenSession
        );

        $productHomeComponentDto = $this->createProductHomeComponentDto(
            $requestDto,
            $productCreateForm,
            $productModifyForm,
            $productRemoveForm,
            $productRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            null,
            null,
            $searchBarForm->getCsrfToken(),
            $productsData['products'],
            $productsData['pages_total']
        );

        return $this->renderTemplate($productHomeComponentDto);
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

    private function getProductsData(
        string $groupId,
        string|null $productNameFilterType,
        string|null $productNameFilterValue,
        string|null $shopNameFilterType,
        string|null $shopNameFilterValue,
        int $page,
        int $pageItems,
        string $tokenSession
    ): array {
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

    private function createProductHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $productCreateForm,
        FormInterface $productModifyForm,
        FormInterface $productRemoveForm,
        FormInterface $productRemoveMultiForm,
        string|null $searchBarProductFieldFilter,
        string|null $searchBarProductFieldValue,
        string|null $searchBarShopFieldFilter,
        string|null $searchBarShopFieldValue,
        string $searchBarCsrfToken,
        array $productsData,
        int $pagesTotal,
    ): HomeSectionComponentDto {
        $productHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $productHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new ProductHomeComponentBuilder())
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
            )
            ->validation(
                !empty($productHomeMessagesError) || !empty($productHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarProductFieldFilter,
                $searchBarProductFieldValue,
                SEARCH_TYPE::PRODUCT,
                $searchBarCsrfToken,
                ProductsEndPoint::GET_PRODUCT_DATA,
                $this->generateUrl('product_home', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ])
            )
            ->productCreateFormModal(
                $productCreateForm->getCsrfToken(),
                $this->generateUrl('product_create', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                ]),
            )
            ->productRemoveMultiFormModal(
                $productRemoveMultiForm->getCsrfToken(),
                ''
                // $this->generateUrl('product_remove', [
                //     'group_name' => $requestDto->groupData->name,
                // ])
            )
            ->productRemoveFormModal(
                $productRemoveForm->getCsrfToken(),
                ''
                // $this->generateUrl('product_remove', [
                //     'group_name' => $requestDto->groupData->name,
                // ])
            )
            ->productModifyFormModal(
                $productModifyForm->getCsrfToken(),
                ''
                // $this->generateUrl('product_modify', [
                //     'group_name' => $requestDto->groupNameUrlEncoded,
                //     'product_name' => self::PRODUCT_NAME_PLACEHOLDER,
                // ]),
            )
            ->build();
    }

    private function renderTemplate(HomeSectionComponentDto $homeSectionComponent): Response
    {
        return $this->render('product/product_home/index.html.twig', [
            'homeSectionComponent' => $homeSectionComponent,
        ]);
    }
}
