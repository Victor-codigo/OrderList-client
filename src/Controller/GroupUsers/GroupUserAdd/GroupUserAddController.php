<?php

declare(strict_types=1);

namespace App\Controller\GroupUsers\GroupUserAdd;

use App\Controller\Request\RequestDto;
use App\Form\Group\GroupUserAdd\GROUP_USER_ADD_FORM_FIELDS;
use App\Form\Group\GroupUserAdd\GroupUserAddForm;
use App\Twig\Components\Group\GroupUserAdd\GroupUserAddComponent;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Form\FormFactory;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Form\FormInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/user/add',
    name: 'group_user_Add',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupUserAddController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupUserAddComponent $groupUserAddComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $groupUserAddForm = $this->formFactory->create(new GroupUserAddForm(), $requestDto->request);

        if ($groupUserAddForm->isSubmitted() && $groupUserAddForm->isValid()) {
            $this->groupUserAdd($groupUserAddForm, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupUserAddComponent->loadValidationOkTranslation()],
            $this->groupUserAddComponent->loadErrorsTranslation($groupUserAddForm->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function groupUserAdd(FormInterface $form, string $tokenSession): void
    {
        $responseData = $this->apiEndpoints->groupUsersAdd(
            $form->getFieldData(GROUP_USER_ADD_FORM_FIELDS::GROUP_ID),
            [$form->getFieldData(GROUP_USER_ADD_FORM_FIELDS::NAME)],
            false,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
