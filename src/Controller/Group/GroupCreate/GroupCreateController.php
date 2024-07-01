<?php

declare(strict_types=1);

namespace App\Controller\Group\GroupCreate;

use App\Controller\Request\RequestDto;
use App\Form\Group\GroupCreate\GROUP_CREATE_FORM_FIELDS;
use App\Form\Group\GroupCreate\GroupCreateForm;
use App\Twig\Components\Group\GroupCreate\GroupCreateComponent;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Form\FormFactory;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/create',
    name: 'group_create',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class GroupCreateController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupCreateComponent $groupCreateComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $groupCreateForm = $this->formFactory->create(new GroupCreateForm(), $requestDto->request);

        if ($groupCreateForm->isSubmitted() && $groupCreateForm->isValid()) {
            $this->createGroup($groupCreateForm, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupCreateComponent->loadValidationOkTranslation()],
            $this->groupCreateComponent->loadErrorsTranslation($groupCreateForm->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function createGroup(FormInterface $form, string $tokenSession): void
    {
        $responseData = $this->apiEndpoints->groupCreate(
            $form->getFieldData(GROUP_CREATE_FORM_FIELDS::NAME),
            $form->getFieldData(GROUP_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(GROUP_CREATE_FORM_FIELDS::IMAGE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
