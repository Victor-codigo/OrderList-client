<?php

declare(strict_types=1);

namespace App\Controller\Group\GroupHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Group\GroupCreate\GroupCreateForm;
use App\Form\Group\GroupModify\GroupModifyForm;
use App\Form\Group\GroupRemoveMulti\GroupRemoveMultiForm;
use App\Form\Group\GroupRemove\GroupRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Twig\Components\Group\GroupHome\GroupHomeComponentBuilder;
use App\Twig\Components\Group\GroupHome\Home\GroupHomeSectionComponentDto;
use App\Twig\Components\Group\GroupHome\ListItem\GroupListItemComponent;
use Common\Adapter\Endpoints\GroupsEndpoint;
use Common\Adapter\Router\RouterSelector;
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
    path: '{_locale}/{section}/page-{page}-{page_items}',
    name: 'group_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
        'page' => '\d+',
        'page_items' => '\d+',
        'section' => 'groups',
    ]
)]
class GroupHomeController extends AbstractController
{
    private const GROUP_ID_PLACEHOLDER = '--group_id--';

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
        $groupCreateForm = $this->formFactory->create(new GroupCreateForm(), $requestDto->request);
        $groupModifyForm = $this->formFactory->create(new GroupModifyForm(), $requestDto->request);
        $groupRemoveForm = $this->formFactory->create(new GroupRemoveForm(), $requestDto->request);
        $groupRemoveMultiForm = $this->formFactory->create(new GroupRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $groupsData = $this->getGroupsData(
            $searchBarFormFields,
            $requestDto->page,
            $requestDto->pageItems,
            $requestDto->getTokenSessionOrFail()
        );

        $groupHomeComponentDto = $this->createGroupHomeComponentDto(
            $requestDto,
            $groupCreateForm,
            $groupModifyForm,
            $groupRemoveForm,
            $groupRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarForm->getCsrfToken(),
            $groupsData['groups'],
            $groupsData['pages_total']
        );

        return $this->renderTemplate($groupHomeComponentDto);
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
                'group_home',
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

    private function getGroupsData(array $searchBarFormFields, int $page, int $pageItems, string $tokenSession): array
    {
        $filterSection = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER];
        $filterText = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER];
        $filterValue = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE];

        $groupsData = $this->endpoints->userGroupsGetData(
            $filterSection,
            $filterText,
            $filterValue,
            $page,
            $pageItems,
            null,
            true,
            $tokenSession
        );

        $groupsData['data']['groups'] = array_map(
            fn (array $groupData) => GroupDataResponse::fromArray($groupData),
            $groupsData['data']['groups']
        );

        return $groupsData['data'];
    }

    private function getGroupsShopsData(string $groupId, array $groupsData, string $tokenSession): array
    {
        $groupsId = array_column($groupsData, 'id');

        if (empty($groupsId)) {
            return [];
        }

        $shopsData = $this->endpoints->shopsGetData(
            $groupId,
            null,
            $groupsId,
            null,
            null,
            null,
            1,
            100,
            true,
            $tokenSession
        );

        return array_map(
            fn (array $shopData) => ShopDataResponse::fromArray($shopData),
            $shopsData['data']['shops']
        );
    }

    /**
     * @param GroupDataResponse[] $groupsData
     */
    private function createGroupHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $groupCreateForm,
        FormInterface $groupModifyForm,
        FormInterface $groupRemoveForm,
        FormInterface $groupRemoveMultiForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        ?string $searchBarSectionFilterValue,
        string $searchBarCsrfToken,
        array $groupsData,
        int $pagesTotal,
    ): GroupHomeSectionComponentDto {
        $groupHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $groupHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );

        return (new GroupHomeComponentBuilder())
            ->title(
                null
            )
            ->errors(
                $groupHomeMessagesOk,
                $groupHomeMessagesError
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                $pagesTotal
            )
            ->listItems(
                $groupsData,
                $this->generateUrl('group_users_home', [
                    'group_name' => GroupListItemComponent::GROUP_USERS_NAME_PLACEHOLDER,
                    'page' => 1,
                    'page_items' => 100,
                    'section' => 'users',
                ]),
                $this->generateUrl('list_orders_home_group', [
                    'group_name' => GroupListItemComponent::GROUP_USERS_NAME_PLACEHOLDER,
                    'page' => 1,
                    'page_items' => 100,
                    'section' => 'list-orders',
                    'group_type' => 'group',
                ]),
                $this->generateUrl('list_orders_home_no_group', [
                    'page' => 1,
                    'page_items' => 100,
                    'section' => 'list-orders',
                ])
            )
            ->validation(
                !empty($groupHomeMessagesError) || !empty($groupHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                GroupsEndpoint::GET_USER_GROUPS_GET_DATA,
                $this->generateUrl('group_home', [
                    'section' => 'groups',
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ]),
                ''
            )
            ->groupCreateFormModal(
                $groupCreateForm->getCsrfToken(),
                $this->generateUrl('group_create'),
            )
            ->groupRemoveMultiFormModal(
                $groupRemoveMultiForm->getCsrfToken(),
                $this->generateUrl('group_remove')
            )
            ->groupRemoveFormModal(
                $groupRemoveForm->getCsrfToken(),
                $this->generateUrl('group_remove')
            )
            ->groupModifyFormModal(
                $groupModifyForm->getCsrfToken(),
                $this->generateUrl('group_modify', [
                    'group_id' => self::GROUP_ID_PLACEHOLDER,
                ]),
            )
            ->build();
    }

    private function renderTemplate(GroupHomeSectionComponentDto $groupHomeSectionComponent): Response
    {
        return $this->render('group/group_home/index.html.twig', [
            'groupHomeSectionComponent' => $groupHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('GroupHomeComponent'),
        ]);
    }
}
