<?php

declare(strict_types=1);

namespace App\Controller\GroupUsers\GroupUsersHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\GroupUserDataResponse;
use App\Form\GroupUsers\GroupUsersAdd\GroupUsersAddForm;
use App\Form\GroupUsers\GroupUsersRemoveMulti\GroupUsersRemoveMultiForm;
use App\Form\GroupUsers\GroupUsersRemove\GroupUsersRemoveForm;
use App\Form\SearchBar\SEARCHBAR_FORM_FIELDS;
use App\Form\SearchBar\SearchBarForm;
use App\Twig\Components\GroupUsers\GroupUsersHome\GroupUsersHomeComponentBuilder;
use App\Twig\Components\GroupUsers\GroupUsersHome\Home\GroupUsersHomeSectionComponentDto;
use Common\Adapter\Endpoints\GroupsEndpoint;
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
    name: 'group_users_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
        'page' => '\d+',
        'page_items' => '\d+',
        'section' => 'users',
    ]
)]
class GroupUsersHomeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GetPageTitleService $getPageTitleService
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $groupUserAddForm = $this->formFactory->create(new GroupUsersAddForm(), $requestDto->request);
        $groupUserRemoveForm = $this->formFactory->create(new GroupUsersRemoveForm(), $requestDto->request);
        $groupUserRemoveMultiForm = $this->formFactory->create(new GroupUsersRemoveMultiForm(), $requestDto->request);
        $searchBarForm = $this->formFactory->create(new SearchBarForm(), $requestDto->request);

        $this->searchBarForm($searchBarForm, $requestDto);
        $searchBarFormFields = $this->getSearchBarFieldsValues(
            $searchBarForm,
            $this->controllerUrlRefererRedirect->getFlashBag($requestDto->request->attributes->get('_route'), FLASH_BAG_TYPE_SUFFIX::DATA),
        );

        $groupUsersData = $this->getGroupUsersData(
            $requestDto->groupData->id,
            $requestDto->page,
            Config::GROUP_USERS_MAX,
            $searchBarFormFields,
            $requestDto->getTokenSessionOrFail()
        );

        $groupHomeComponentDto = $this->createGroupHomeComponentDto(
            $requestDto,
            $groupUserAddForm,
            $groupUserRemoveForm,
            $groupUserRemoveMultiForm,
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER],
            $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER],
            $searchBarForm->getCsrfToken(),
            $groupUsersData['users'],
            $groupUsersData['pages_total']
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
                'group_users_home',
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

    private function getGroupUsersData(string $groupId, int $page, int $pageItems, array $searchBarFormFields, string $tokenSession): array
    {
        $filterSection = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SECTION_FILTER];
        $filterText = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::NAME_FILTER];
        $filterValue = $searchBarFormFields[SEARCHBAR_FORM_FIELDS::SEARCH_VALUE];

        $groupUsersData = $this->endpoints->groupGetUsersData(
            $groupId,
            $page,
            $pageItems,
            $filterSection,
            $filterText,
            $filterValue,
            true,
            $tokenSession
        );

        $groupUsersData['data']['users'] = array_map(
            fn (array $groupUserData) => GroupUserDataResponse::fromArray($groupUserData),
            $groupUsersData['data']['users']
        );

        return $groupUsersData['data'];
    }

    private function createGroupHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $groupUserAddForm,
        FormInterface $groupUserRemoveForm,
        FormInterface $groupUserRemoveMultiForm,
        ?string $searchBarSearchValue,
        ?string $searchBarNameFilterValue,
        ?string $searchBarSectionFilterValue,
        string $searchBarCsrfToken,
        array $groupUsersData,
        int $pagesTotal,
    ): GroupUsersHomeSectionComponentDto {
        $groupHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_ERROR->value
        );
        $groupHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').FLASH_BAG_TYPE_SUFFIX::MESSAGE_OK->value
        );
        $isUserSessionAdminOfTheGroup = $this->isUserSessionAdminOfTheGroup($requestDto->getUserSessionData()->id, $groupUsersData);

        return (new GroupUsersHomeComponentBuilder())
            ->title(
                $requestDto->groupData->name,
                null
            )
            ->groupUserGrants(
                $requestDto->groupData->id
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
                $groupUsersData,
                $requestDto->getUserSessionData()->id,
                $isUserSessionAdminOfTheGroup
            )
            ->validation(
                !empty($groupHomeMessagesError) || !empty($groupHomeMessagesOk) ? true : false,
            )
            ->searchBar(
                $requestDto->groupData->id,
                $searchBarSearchValue,
                $searchBarSectionFilterValue,
                $searchBarNameFilterValue,
                $searchBarCsrfToken,
                GroupsEndpoint::GET_USER_GROUPS_GET_DATA,
                $this->generateUrl('group_users_home', [
                    'group_name' => $requestDto->groupNameUrlEncoded,
                    'section' => 'users',
                    'page' => $requestDto->page,
                    'page_items' => $requestDto->pageItems,
                ]),
                ''
            )
            ->groupUserAddFormModal(
                $requestDto->groupData->id,
                $groupUserAddForm->getCsrfToken(),
                $this->generateUrl('group_user_add'),
            )
            ->groupUsersRemoveMultiFormModal(
                $requestDto->groupData->id,
                $groupUserRemoveMultiForm->getCsrfToken(),
                $this->generateUrl('group_user_remove')
            )
            ->groupUsersRemoveFormModal(
                $requestDto->groupData->id,
                $groupUserRemoveForm->getCsrfToken(),
                $this->generateUrl('group_user_remove')
            )
            ->display(
                !$isUserSessionAdminOfTheGroup
            )
            ->build();
    }

    /**
     * @param GroupUserDataResponse[] $groupUsersData
     */
    private function isUserSessionAdminOfTheGroup(string $userSessionId, array $groupUsersData): bool
    {
        $groupUserDataSession = array_values(array_filter(
            $groupUsersData,
            fn (GroupUserDataResponse $groupUserData) => $groupUserData->id === $userSessionId
        ));

        if (empty($groupUserDataSession)) {
            return false;
        }

        if ($groupUserDataSession[0]->admin) {
            return true;
        }

        return false;
    }

    private function renderTemplate(GroupUsersHomeSectionComponentDto $groupUsersHomeSectionComponent): Response
    {
        return $this->render('group_users/group_users_home/index.html.twig', [
            'groupUsersHomeSectionComponent' => $groupUsersHomeSectionComponent,
            'pageTitle' => $this->getPageTitleService->__invoke('GroupUsersHomeComponent'),
            'domainName' => Config::CLIENT_DOMAIN_NAME,
        ]);
    }
}
