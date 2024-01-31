<?php

namespace App\Controller;

use App\Form\Group\GroupUserAdd\GROUP_USER_ADD_FORM_ERRORS;
use App\Form\Group\GroupUserAdd\GROUP_USER_ADD_FORM_FIELDS;
use App\Form\Group\GroupUserAdd\GroupUserAddForm;
use App\Twig\Components\Group\GroupUserAdd\GroupUserAddComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/{group_id}/user-add',
    name: 'group_user_add',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupUserAddController extends AbstractController
{
    private const GROUP_USER_ADD_ENDPOINT = '/api/v1/groups/user';
    private const GROUP_DATA_ENDPOINT = '/api/v1/groups/data';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        try {
            $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);
            $groupId = $request->attributes->get('group_id');
            $form = $this->formFactory->create(new GroupUserAddForm(), $request);
            $groupData = $this->getResponseRequest($form, $groupId, '', $tokenSession, $this->requestGroupData(...))[0];
            $formSubmitted = false;

            if ($form->isSubmitted() && $form->isValid()) {
                $this->getResponseRequest($form, $groupId, $groupData['name'], $tokenSession, $this->requestGroupUserAdd(...));
                $formSubmitted = true;
            }

            return $this->renderGroupCreateComponent($form, $groupData['name'], $formSubmitted);
        } catch (\Exception) {
            if (!isset($groupData[0])) {
                throw new NotFoundHttpException();
            }
        }
    }

    private function requestGroupUserAdd(FormInterface $form, string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_USER_ADD_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                    'group_id' => $groupId,
                    'identifier_type' => 'name',
                    'users' => [
                        $form->getFieldData(GROUP_USER_ADD_FORM_FIELDS::NAME),
                    ],
                ],
                $tokenSession
            )
        );
    }

    private function requestGroupData(FormInterface $form, string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_DATA_ENDPOINT."/{$groupId}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession),
        );
    }

    private function getResponseRequest(FormInterface $form, string $groupId, string $groupName, string $tokenSession, callable $requestCallback): array
    {
        try {
            $response = $requestCallback($form, $groupId, $tokenSession);

            $responseData = $response->toArray();

            return $responseData['data'];
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorDescription) {
                $form->addError($error, $errorDescription);
            }

            return [];
        } catch (Error500Exception|NetworkException) {
            $form->addError(GROUP_USER_ADD_FORM_ERRORS::INTERNAL_SERVER->value);

            return [];
        }
    }

    private function renderGroupCreateComponent(FormInterface $form, string $groupName, bool $formSubmitted): Response
    {
        $groupUserAddComponentData = new GroupUserAddComponentDto(
            $form->getErrors(),
            $form->getFieldData(GROUP_USER_ADD_FORM_FIELDS::NAME),
            $form->getFieldData(GROUP_USER_ADD_FORM_FIELDS::GROUP_ID),
            $groupName,
            $form->getCsrfToken(),
            $formSubmitted
        );

        return new Response($this->renderView('group/group_user_add/index.html.twig', [
            'GroupUserAddComponent' => $groupUserAddComponentData,
        ]));
    }
}
