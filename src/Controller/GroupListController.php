<?php

namespace App\Controller;

use App\Form\Group\GroupList\GroupRemoveForm;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_ERRORS;
use App\Twig\Components\Group\GroupList\ListItem\GroupListItemComponentDto;
use App\Twig\Components\Group\GroupList\List\GroupListComponentDto;
use App\Twig\Components\Group\GroupRemove\GroupRemoveComponentDto;
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
    path: '{_locale}/group/list/page/{page}',
    name: 'group_list',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupListController extends AbstractController
{
    private const GROUP_LIST_ENDPOINT = '/api/v1/groups/user-groups';
    private const GROUP_REMOVE_ENDPOINT = '/api/v1/groups';
    private const GROUP_IMAGE_NOT_SET = '/assets/img/common/group-avatar-no-image.svg';

    private int $pageCurrent;

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $formRemove = $this->formFactory->create(new GroupRemoveForm(), $request);
        $tokenSession = $request->cookies->get('TOKENSESSION');
        $this->pageCurrent = $request->attributes->getInt('page');
        $userGroupsData = $this->getUserGroups($formRemove, $tokenSession);
        $formRemoveValid = false;

        if ($formRemove->isSubmitted() && $formRemove->isValid()) {
            $this->formRemoveValid($formRemove, $tokenSession);
            $formRemoveValid = true;
        }

        return $this->renderGroupListComponent($formRemove, $userGroupsData, $formRemoveValid);
    }

    private function requestGroupList(string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_LIST_ENDPOINT."?page={$this->pageCurrent}&page_items=1".'&'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession),
        );
    }

    private function getUserGroups(FormInterface $form, string $tokenSession): array
    {
        try {
            $response = $this->requestGroupList($tokenSession);
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

    private function requestGroupRemove(FormInterface $formData, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_REMOVE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                    'group_id' => $formData->getFieldData('group_id'),
                ],
                $tokenSession
            ),
        );
    }

    private function formRemoveValid(FormInterface $form, string $tokenSession): void
    {
        try {
            $response = $this->requestGroupRemove($form, $tokenSession);
            $responseData = $response->toArray();
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorDescription) {
                $form->addError($error, $errorDescription);
            }
        } catch (Error500Exception|NetworkException) {
            $form->addError(GROUP_REMOVE_FORM_ERRORS::INTERNAL_SERVER->value);
        }
    }

    /**
     * @return GroupListItemComponentDto[]
     */
    private function getListComponentData(array $groupListItemsData): array
    {
        return array_map(
            fn (array $groupData) => new GroupListItemComponentDto(
                $groupData['group_id'],
                $groupData['name'],
                $groupData['description'],
                null === $groupData['image'] ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET : $groupData['image'],
                $groupData['admin']
            ),
            $groupListItemsData
        );
    }

    private function getPaginatorData(int $page, int $pagesTotal): PaginatorComponentDto
    {
        return new PaginatorComponentDto($page, $pagesTotal, '{pageNum}');
    }

    private function getGroupRemoveModalData(string $csrfToken): ModalComponentDto
    {
        $groupRemoveDto = new GroupRemoveComponentDto([], '', $csrfToken);

        return new ModalComponentDto(
            'group_remove_modal',
            '',
            false,
            'GroupRemoveComponent',
            $groupRemoveDto,
            []
        );
    }

    private function renderGroupListComponent(FormInterface $formRemove, array $groupListData, bool $removeFormValid): Response
    {
        if (empty($groupListData)) {
            $groupListData['groups'] = [];
            $groupListData['page'] = 1;
            $groupListData['page_items'] = 10;
            $groupListData['pages_total'] = 1;
        }

        $groupListComponentData = new GroupListComponentDto(
            $formRemove->getErrors(),
            $this->getListComponentData($groupListData['groups']),
            $this->getPaginatorData($groupListData['page'], $groupListData['pages_total']),
            $this->getGroupRemoveModalData($formRemove->getCsrfToken()),
            $removeFormValid
        );

        $groupListRendered = $this->renderView('group/group_list/index.html.twig', [
            'GroupListComponent' => $groupListComponentData,
        ]);

        return new Response($groupListRendered);
    }
}
