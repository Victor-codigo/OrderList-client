<?php

namespace App\Controller;

use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_ERRORS;
use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_FIELDS;
use App\Form\Group\GroupCreate\GroupCreateForm;
use App\Twig\Components\Group\GroupCreate\GroupCreateComponentDto;
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
    path: '{_locale}/group/create',
    name: 'group_create',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupCreateController extends AbstractController
{
    private const GROUP_CREATE_ENDPOINT = '/api/v1/groups';

    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient
    ) {
    }

    public function __invoke(Request $request): Response
    {
        $form = $this->formFactory->create(new GroupCreateForm(), $request);
        $tokenSession = $request->cookies->get(HTTP_CLIENT_CONFIGURATION::COOKIE_SESSION_NAME);

        if ($form->isSubmitted() && $form->isValid()) {
            return $this->formValid($form, $tokenSession);
        }

        return $this->formNotValid($form);
    }

    private function requestGroupCreate(array $formData, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::GROUP_CREATE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::form([
                    'name' => $formData[GROUP_CREATE_FORM_FIELDS::NAME],
                    'description' => $formData[GROUP_CREATE_FORM_FIELDS::DESCRIPTION],
                    'type' => 'TYPE_GROUP',
                ],
                [
                    'image' => $formData[GROUP_CREATE_FORM_FIELDS::IMAGE],
                ],
                $tokenSession
            )
        );
    }

    private function formValid(FormInterface $form, string $tokenSession): Response
    {
        try {
            $formData = $form->getData();
            $response = $this->requestGroupCreate($formData, $tokenSession);
            $response->toArray();
        } catch (Error400Exception $e) {
            $responseData = $e->getResponse()->toArray(false);
            foreach ($responseData['errors'] as $error => $errorDescription) {
                $form->addError($error, $errorDescription);
            }
        } catch (Error500Exception|NetworkException) {
            $form->addError(GROUP_CREATE_FORM_ERRORS::INTERNAL_SERVER->value);
        } finally {
            return new Response($this->renderGroupCreateComponent($form, true));
        }
    }

    private function formNotValid(FormInterface $form): Response
    {
        return new Response($this->renderGroupCreateComponent($form, false));
    }

    private function renderGroupCreateComponent(FormInterface $form, bool $formSubmitted): string
    {
        $formData = $form->getData();

        $groupCreateComponentData = new GroupCreateComponentDto(
            $form->getErrors(),
            $formData[GROUP_CREATE_FORM_FIELDS::NAME],
            $formData[GROUP_CREATE_FORM_FIELDS::DESCRIPTION],
            $form->getCsrfToken(),
            $formSubmitted
        );

        return $this->renderView('group/group_create/index.html.twig', [
            'GroupCreateComponent' => $groupCreateComponentData,
        ]);
    }
}
