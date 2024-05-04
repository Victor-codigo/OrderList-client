<?php

declare(strict_types=1);

namespace App\Controller\GroupUsers\GroupUsersRemove;

use App\Controller\Request\RequestDto;
use App\Form\GroupUsers\GroupUsersRemoveMulti\GROUP_USERS_REMOVE_MULTI_FORM_FIELDS;
use App\Form\GroupUsers\GroupUsersRemoveMulti\GroupUsersRemoveMultiForm;
use App\Form\GroupUsers\GroupUsersRemove\GROUP_USERS_REMOVE_FORM_FIELDS;
use App\Form\GroupUsers\GroupUsersRemove\GroupUsersRemoveForm;
use App\Twig\Components\GroupUsers\GroupUsersRemove\GroupUsersRemoveComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/user/remove',
    name: 'group_user_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupUsersRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupUsersRemoveComponent $groupUsersRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $groupUserRemoveForm = $this->groupUserRemoveForm($requestDto);
        $groupUserRemoveMultiForm = $this->groupUserRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupUsersRemoveComponent->loadValidationOkTranslation()],
            $this->groupUsersRemoveComponent->loadErrorsTranslation(
                array_merge($groupUserRemoveForm->getErrors(), $groupUserRemoveMultiForm->getErrors())
            ),
            []
        );
    }

    private function groupUserRemoveForm(RequestDto $requestDto): FormInterface
    {
        $groupUserRemoveForm = $this->formFactory->create(new GroupUsersRemoveForm(), $requestDto->request);

        if (!$groupUserRemoveForm->isSubmitted() || !$groupUserRemoveForm->isValid()) {
            return $groupUserRemoveForm;
        }

        $this->requestGroupRemove(
            $groupUserRemoveForm,
            $groupUserRemoveForm->getFieldData(GROUP_USERS_REMOVE_FORM_FIELDS::GROUP_ID, ''),
            $groupUserRemoveForm->getFieldData(GROUP_USERS_REMOVE_FORM_FIELDS::USERS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupUserRemoveForm;
    }

    private function groupUserRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $groupUserRemoveMultiForm = $this->formFactory->create(new GroupUsersRemoveMultiForm(), $requestDto->request);

        if (!$groupUserRemoveMultiForm->isSubmitted() || !$groupUserRemoveMultiForm->isValid()) {
            return $groupUserRemoveMultiForm;
        }

        $this->requestGroupRemove(
            $groupUserRemoveMultiForm,
            $groupUserRemoveMultiForm->getFieldData(GROUP_USERS_REMOVE_MULTI_FORM_FIELDS::GROUP_ID, ''),
            $groupUserRemoveMultiForm->getFieldData(GROUP_USERS_REMOVE_MULTI_FORM_FIELDS::USERS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupUserRemoveMultiForm;
    }

    private function requestGroupRemove(FormInterface $form, string $groupId, array $usersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->groupUserRemove($groupId, $usersId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
