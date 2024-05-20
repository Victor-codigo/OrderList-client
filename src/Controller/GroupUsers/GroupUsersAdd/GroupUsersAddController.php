<?php

declare(strict_types=1);

namespace App\Controller\GroupUsers\GroupUsersAdd;

use App\Controller\Request\RequestDto;
use App\Form\GroupUsers\GroupUsersAdd\GROUP_USERS_ADD_FORM_FIELDS;
use App\Form\GroupUsers\GroupUsersAdd\GroupUsersAddForm;
use App\Twig\Components\GroupUsers\GroupUsersAdd\GroupUsersAddComponent;
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
    path: '{_locale}/group/user/add',
    name: 'group_user_add',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class GroupUsersAddController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupUsersAddComponent $groupUsersAddComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $groupUserAddForm = $this->formFactory->create(new GroupUsersAddForm(), $requestDto->request);

        if ($groupUserAddForm->isSubmitted() && $groupUserAddForm->isValid()) {
            $this->groupUserAdd($groupUserAddForm, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupUsersAddComponent->loadValidationOkTranslation()],
            $this->groupUsersAddComponent->loadErrorsTranslation($groupUserAddForm->getErrors()),
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
            $form->getFieldData(GROUP_USERS_ADD_FORM_FIELDS::GROUP_ID),
            [$form->getFieldData(GROUP_USERS_ADD_FORM_FIELDS::NAME)],
            false,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
