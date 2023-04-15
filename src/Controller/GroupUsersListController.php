<?php

namespace App\Controller;

use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_ERRORS;
use App\Form\Group\GroupUserRemove\GROUP_USER_REMOVE_FORM_FIELDS;
use App\Form\Group\GroupUserRemove\GroupUserRemoveForm;
use App\Twig\Components\Group\GroupList\ListItem\GroupListItemComponentDto;
use App\Twig\Components\Group\GroupUserRemove\GroupUserRemoveComponentDto;
use App\Twig\Components\Group\GroupUsersList\ListItem\GroupUsersListItemComponentDto;
use App\Twig\Components\Group\GroupUsersList\List\GroupUsersListComponentDto;
use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\Paginator\PaginatorComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\DecodingException;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/{group_id}/users/list/page/{page}',
    name: 'group_list',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupUsersListController extends AbstractController
{
    private const GROUP_USERS_LIST_ENDPOINT = '/api/v1/groups/user';
    private const GROUP_DATA_ENDPOINT = '/api/v1/groups/data';
    private const GROUP_GET_ADMINS_ENDPOINT = '/api/v1/groups/admins';
    private const GROUP_USER_REMOVE_ENDPOINT = '/api/v1/groups/user';
    private const GROUP_IMAGE_NOT_SET = '/assets/img/common/user-avatar-no-image.svg';
    private const PAGE_ITEMS = 20;

    private int $pageCurrent;
    private int $pageItems = self::PAGE_ITEMS;

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $formUserRemove = $this->formFactory->create(new GroupUserRemoveForm(), $request);
        $tokenSession = $request->cookies->get('TOKENSESSION');
        $this->pageCurrent = $request->attributes->getInt('page');
        $groupId = $request->attributes->get('group_id');
        $formRemoveValid = false;

        if ($formUserRemove->isSubmitted() && $formUserRemove->isValid()) {
            $this->getGroupData($formUserRemove, $groupId, $tokenSession, $this->requestGroupUserRemove(...));
            $formRemoveValid = true;
        }

        $userGroupsData = $this->getGroupData($formUserRemove, $groupId, $tokenSession, $this->requestGroupUsersList(...));
        $groupData = $this->getGroupData($formUserRemove, $groupId, $tokenSession, $this->requestGroupData(...))[0];
        $groupAdmins = $this->getGroupData($formUserRemove, $groupId, $tokenSession, $this->requestGroupAdmins(...));

        return $this->renderGroupListComponent($formUserRemove, $userGroupsData, $groupData, $groupAdmins['is_admin'], $formRemoveValid);
    }

    private function requestGroupUsersList(string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_USERS_LIST_ENDPOINT."/{$groupId}?page={$this->pageCurrent}&page_items={$this->pageItems}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession),
        );
    }

    private function requestGroupData(string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_DATA_ENDPOINT."/{$groupId}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession),
        );
    }

    private function requestGroupAdmins(string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_GET_ADMINS_ENDPOINT."/{$groupId}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession),
        );
    }

    private function requestGroupUserRemove(string $groupId, string $tokenSession, string $userId): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_USER_REMOVE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                    'group_id' => $groupId,
                    'users' => [
                        $userId,
                    ],
                ],
                $tokenSession
            ),
        );
    }

    private function getGroupData(FormInterface $form, string $groupId, string $tokenSession, callable $requestCallback): array
    {
        try {
            $response = $requestCallback($groupId, $tokenSession, $form->getFieldData(GROUP_USER_REMOVE_FORM_FIELDS::USER_ID));
            $responseData = $response->toArray();

            return $responseData['data'];
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorDescription) {
                $form->addError($error, $errorDescription);
            }

            return [];
        } catch (Error500Exception|NetworkException) {
            $form->addError(GROUP_REMOVE_FORM_ERRORS::INTERNAL_SERVER->value);

            return [];
        } catch (DecodingException $e) {
            return [];
        }
    }

    /**
     * @return GroupListItemComponentDto[]
     */
    private function getListUsersComponentData(string $groupId, array $groupUsersListItemsData, bool $userSessionIsAdmin): array
    {
        return array_map(
            fn (array $groupUserData) => new GroupUsersListItemComponentDto(
                $groupId,
                $groupUserData['id'],
                $groupUserData['name'],
                null === $groupUserData['image'] ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET : $groupUserData['image'],
                $groupUserData['admin'],
                $userSessionIsAdmin
            ),
            $groupUsersListItemsData
        );
    }

    private function getPaginatorData(int $page, int $pagesTotal): PaginatorComponentDto
    {
        return new PaginatorComponentDto($page, $pagesTotal, '{pageNum}');
    }

    private function getGroupUserRemoveModalData(string $csrfToken): ModalComponentDto
    {
        $groupUserRemoveDto = new GroupUserRemoveComponentDto([], '', '', $csrfToken);

        return new ModalComponentDto(
            'group_user_remove_modal',
            '',
            false,
            'GroupUserRemoveComponent',
            $groupUserRemoveDto,
            []
        );
    }

    private function renderGroupListComponent(FormInterface $formRemove, array $groupUsersListData, array $groupData, bool $userSessionIsAdmin, bool $removeFormValid): Response
    {
        if (empty($groupUsersListData)) {
            $groupUsersListData['users'] = [];
            $groupUsersListData['page'] = 1;
            $groupUsersListData['page_items'] = 10;
            $groupUsersListData['pages_total'] = 1;
        }

        if (empty($groupData)) {
            $groupData['group_id'] = '';
            $groupData['name'] = '';
        }

        $groupUsersListComponentData = new GroupUsersListComponentDto(
            $formRemove->getErrors(),
            $this->getListUsersComponentData($groupData['group_id'], $groupUsersListData['users'], $userSessionIsAdmin),
            $groupData['name'],
            $this->getPaginatorData($groupUsersListData['page'], $groupUsersListData['pages_total']),
            $this->getGroupUserRemoveModalData($formRemove->getCsrfToken()),
            $removeFormValid
        );

        $groupListRendered = $this->renderView('group/group_users_list/index.html.twig', [
            'GroupUsersListComponent' => $groupUsersListComponentData,
        ]);

        return new Response($groupListRendered);
    }
}
