<?php

namespace App\Controller;

use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_FIELDS;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_ERRORS;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_FIELDS;
use App\Form\Group\GroupModify\GroupModifyForm;
use App\Twig\Components\Group\GroupModify\GroupModifyComponentDto;
use Common\Adapter\Form\FormFactory;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpCllent\HttpClientInterface;
use Common\Domain\Ports\HttpCllent\HttpClientResponseInteface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/modify/{group_id}',
    name: 'group_modify',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupModifyController extends AbstractController
{
    private const GROUP_MODIFY_ENDPOINT = '/api/v1/groups/modify';
    private const GROUP_GET_DATA_ENDPOINT = '/api/v1/groups/data/';
    private const GROUP_IMAGE_NOT_SET = '/assets/img/common/group-avatar-no-image.svg';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new GroupModifyForm(), $request);
        $tokenSession = $request->cookies->get('TOKENSESSION');
        $groupId = $request->attributes->get('group_id');

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form, $groupId, $tokenSession);
        }

        return $this->formNotValid($form, $groupId, $tokenSession);
    }

    private function requestGroupModify(array $formData, string $tokenSession): HttpClientResponseInteface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_MODIFY_ENDPOINT.'?XDEBUG_SESSION=VSCODE',
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

    private function requestGroupData(string $groupId, string $tokenSession): HttpClientResponseInteface
    {
        return $this->httpClient->request(
            'GET',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_GET_DATA_ENDPOINT.$groupId,
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    private function getGroupData(string $groupId, string $tokenSession): array|null
    {
        try {
            $response = $this->requestGroupData($groupId, $tokenSession);
            $responseData = $response->toArray();

            return $responseData['data'][0];
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            return null;
        }
    }

    private function formValid(FormInterface $form, string $groupId, string $tokenSession): Response
    {
        try {
            $formData = $form->getData();
            $response = $this->requestGroupModify($formData, $tokenSession);
            $response->toArray();
        } catch (Error400Exception $e) {
            array_walk(
                $e->getResponse()->toArray(false)['errors'],
                fn (string $errorDescription, string $error) => $form->addError($error, $errorDescription)
            );
        } catch (Error500Exception|NetworkException $e) {
            $form->addError(GROUP_MODIFY_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            $groupData = $this->getGroupData($groupId, $tokenSession);

            return new Response($this->renderGroupCreateComponent($form, $groupData));
        }
    }

    private function formNotValid(FormInterface $form, string $groupId, string $tokenSession): Response
    {
        $groupData = $this->getGroupData($groupId, $tokenSession);

        return new Response($this->renderGroupCreateComponent($form, $groupData));
    }

    private function renderGroupCreateComponent(FormInterface $form, array $groupData): string
    {
        $groupModifyComponentData = new GroupModifyComponentDto(
            $form->getErrors(),
            $groupData[GROUP_MODIFY_FORM_FIELDS::GROUP_ID],
            $groupData[GROUP_MODIFY_FORM_FIELDS::NAME],
            $groupData[GROUP_MODIFY_FORM_FIELDS::DESCRIPTION],
            null === $groupData['image'] ? HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET : HTTP_CLIENT_CONFIGURATION::API_DOMAIN.$groupData['image'],
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_IMAGE_NOT_SET,
            $form->getCsrfToken()
        );

        return $this->renderView('group/group_modify/index.html.twig', [
            'GroupModifyComponent' => $groupModifyComponentData,
        ]);
    }
}
