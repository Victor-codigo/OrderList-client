<?php

namespace App\Controller;

use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_FIELDS;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_ERRORS;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_FIELDS;
use App\Form\Group\GroupModify\GroupModifyForm;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_FIELDS;
use App\Form\Group\GroupRemove\GroupRemoveForm;
use App\Twig\Components\Group\GroupModify\GroupModifyComponentDto;
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
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/modify/{group_id}',
    name: 'group_modify',
    methods: ['GET', 'POST', 'DELETE'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupModifyController extends AbstractController
{
    private const GROUP_MODIFY_ENDPOINT = '/api/v1/groups/modify';
    private const GROUP_GET_DATA_ENDPOINT = '/api/v1/groups/data/';
    private const GROUP_REMOVE_ENDPOINT = '/api/v1/groups';
    private const GROUP_IMAGE_NOT_SET = '/assets/img/common/group-avatar-no-image.svg';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $groupModifyForm = $this->formFactory->create(new GroupModifyForm(), $request);
        $groupRemoveForm = $this->formFactory->create(new GroupRemoveForm(), $request);
        $tokenSession = $request->cookies->get('TOKENSESSION');
        $groupId = $request->attributes->get('group_id');
        $submitted = false;
        $errorList = [];

        if ($groupModifyForm->isSubmitted() && $groupModifyForm->isValid()) {
            $errorList = $this->formManagement($this->requestGroupModify(...), $groupModifyForm, $tokenSession);
            $submitted = true;
        } elseif ($groupRemoveForm->isSubmitted() && $groupRemoveForm->isValid()) {
            $errorList = $this->formManagement($this->requestGroupRemove(...), $groupRemoveForm, $tokenSession);
            $submitted = true;
        }

        $groupData = $this->getGroupData($groupId, $tokenSession);
        $this->addFormErrors($groupModifyForm, $errorList);

        return $this->renderGroupCreateComponent($groupModifyForm, $groupRemoveForm, $groupData, $submitted);
    }

    private function addFormErrors(FormInterface $form, array $errorList): void
    {
        foreach ($errorList as $error => $errorValue) {
            $form->addError($error, $errorValue);
        }
    }

    private function requestGroupModify(FormInterface $form, string $tokenSession): HttpClientResponseInterface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_MODIFY_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::form([
                    'group_id' => $formData[GROUP_MODIFY_FORM_FIELDS::GROUP_ID],
                    'name' => $formData[GROUP_CREATE_FORM_FIELDS::NAME],
                    'description' => $formData[GROUP_CREATE_FORM_FIELDS::DESCRIPTION],
                    'image_remove' => $formData[GROUP_MODIFY_FORM_FIELDS::IMAGE_REMOVE],
                    '_method' => 'PUT',
                ],
                [
                    'image' => $formData[GROUP_CREATE_FORM_FIELDS::IMAGE],
                ],
                $tokenSession
            )
        );
    }

    private function requestGroupRemove(FormInterface $form, string $tokenSession): HttpClientResponseInterface
    {
        $formData = $form->getData();

        return $this->httpClient->request(
            'DELETE',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_REMOVE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                    'group_id' => $formData[GROUP_REMOVE_FORM_FIELDS::GROUP_ID],
                ],
                $tokenSession
            )
        );
    }

    private function requestGroupData(string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_GET_DATA_ENDPOINT.$groupId,
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    private function formManagement(callable $requestCallback, ...$requestCallbackArguments): array
    {
        try {
            $response = $requestCallback(...$requestCallbackArguments);
            $responseData = $response->toArray();

            return [];
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);

            return isset($responseData['errors']) ? $responseData['errors'] : [];
        } catch (Error500Exception|NetworkException) {
            return [GROUP_MODIFY_FORM_ERRORS::INTERNAL_SERVER];
        }
    }

    private function getGroupData(string $groupId, string $tokenSession): array|null
    {
        $response = $this->requestGroupData($groupId, $tokenSession);
        $responseData = $response->toArray();

        return $responseData['data'][0];
    }

    private function renderGroupCreateComponent(FormInterface $groupModifyForm, FormInterface $groupRemoveForm, array $groupData, bool $formSubmitted): Response
    {
        $groupModifyComponentData = new GroupModifyComponentDto(
            $groupModifyForm->getErrors(),
            $groupData[GROUP_MODIFY_FORM_FIELDS::GROUP_ID],
            $groupData[GROUP_MODIFY_FORM_FIELDS::NAME],
            $groupData[GROUP_MODIFY_FORM_FIELDS::DESCRIPTION],
            null === $groupData['image'] ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET : HTTP_CLIENT_CONFIGURATION::API_DOMAIN.$groupData['image'],
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET,
            $groupModifyForm->getCsrfToken(),
            $groupRemoveForm->getCsrfToken(),
            $formSubmitted
        );

        return $this->render('group/group_modify/index.html.twig', [
            'GroupModifyComponent' => $groupModifyComponentData,
        ]);
    }
}
