<?php

declare(strict_types=1);

namespace App\Controller\GroupUsers\GroupUsersRemove;

use App\Controller\Request\RequestDto;
use App\Controller\Request\RequestRefererDto;
use App\Form\GroupUsers\GroupUsersRemove\GROUP_USERS_REMOVE_FORM_FIELDS;
use App\Form\GroupUsers\GroupUsersRemove\GroupUsersRemoveForm;
use App\Form\GroupUsers\GroupUsersRemoveMulti\GROUP_USERS_REMOVE_MULTI_FORM_FIELDS;
use App\Form\GroupUsers\GroupUsersRemoveMulti\GroupUsersRemoveMultiForm;
use App\Twig\Components\GroupUsers\GroupUsersRemove\GroupUsersRemoveComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\JwtToken\JwtToken;
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
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class GroupUsersRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupUsersRemoveComponent $groupUsersRemoveComponent,
        private JwtToken $jwtToken
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $groupUserRemoveForm ??= $this->groupUserRemoveForm($requestDto);
        $groupUserRemoveForm ??= $this->groupUserRemoveMultiForm($requestDto);

        return $this->redirectToRouteAfterAction(
            $groupUserRemoveForm->getFieldData(GROUP_USERS_REMOVE_FORM_FIELDS::GROUP_ID, ''),
            $requestDto->requestReferer,
            $groupUserRemoveForm->getErrors(),
            $requestDto->getTokenSessionOrFail()
        );
    }

    private function redirectToRouteAfterAction(string $groupId, RequestRefererDto $routeReferer, array $formErrors, string $tokenSession): Response
    {
        if (!empty($formErrors)) {
            return $this->redirectToARoute($routeReferer->routeName, $routeReferer->params, $formErrors);
        }

        $userSessionIsInTheGroup = $this->isUserSessionInGroup($groupId, $tokenSession);

        if (!$userSessionIsInTheGroup) {
            return $this->redirectToGroupsHome([]);
        }

        return $this->redirectToARoute($routeReferer->routeName, $routeReferer->params, $formErrors);
    }

    /**
     * @param string[] $formErrors
     */
    private function redirectToGroupsHome(array $formErrors): Response
    {
        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            'group_home',
            [
                'section' => 'groups',
                'page' => 1,
                'page_items' => 100,
            ],
            [$this->groupUsersRemoveComponent->loadValidationRevedOwnUserOkTranslation()],
            $this->groupUsersRemoveComponent->loadErrorsTranslation(
                $formErrors
            ),
            []
        );
    }

    /**
     * @param string[] $formErrors
     */
    private function redirectToARoute(string $routeName, array $routeParams, array $formErrors): Response
    {
        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $routeName,
            $routeParams,
            [$this->groupUsersRemoveComponent->loadValidationOkTranslation()],
            $this->groupUsersRemoveComponent->loadErrorsTranslation(
                $formErrors
            ),
            []
        );
    }

    private function groupUserRemoveForm(RequestDto $requestDto): ?FormInterface
    {
        $groupUserRemoveForm = $this->formFactory->create(new GroupUsersRemoveForm(), $requestDto->request);

        if (!$groupUserRemoveForm->isSubmitted() || !$groupUserRemoveForm->isValid()) {
            return null;
        }

        $this->requestGroupRemove(
            $groupUserRemoveForm,
            $groupUserRemoveForm->getFieldData(GROUP_USERS_REMOVE_FORM_FIELDS::GROUP_ID, ''),
            $groupUserRemoveForm->getFieldData(GROUP_USERS_REMOVE_FORM_FIELDS::USERS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupUserRemoveForm;
    }

    private function groupUserRemoveMultiForm(RequestDto $requestDto): ?FormInterface
    {
        $groupUserRemoveMultiForm = $this->formFactory->create(new GroupUsersRemoveMultiForm(), $requestDto->request);

        if (!$groupUserRemoveMultiForm->isSubmitted() || !$groupUserRemoveMultiForm->isValid()) {
            return null;
        }

        $this->requestGroupRemove(
            $groupUserRemoveMultiForm,
            $groupUserRemoveMultiForm->getFieldData(GROUP_USERS_REMOVE_MULTI_FORM_FIELDS::GROUP_ID, ''),
            $groupUserRemoveMultiForm->getFieldData(GROUP_USERS_REMOVE_MULTI_FORM_FIELDS::USERS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupUserRemoveMultiForm;
    }

    private function isUserSessionInGroup(string $groupId, string $tokenSession): bool
    {
        try {
            $userSessionId = $this->jwtToken->getUserName($tokenSession);
            $groupUsersData = $this->requestGroupUsers($groupId, $tokenSession);

            $groupUsersDataOfUserSession = array_filter(
                $groupUsersData,
                fn (array $groupUserData) => $groupUserData['id'] === $userSessionId
            );

            if (empty($groupUsersDataOfUserSession)) {
                return false;
            }

            return true;
        } catch (\DomainException $e) {
            return false;
        }
    }

    private function requestGroupRemove(FormInterface $form, string $groupId, array $usersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->groupUserRemove($groupId, $usersId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }

    /**
     * @throws \DomainException
     */
    private function requestGroupUsers(string $groupId, string $tokenSession): array
    {
        $responseData = $this->endpoints->groupGetUsersData(
            $groupId,
            1,
            Config::GROUP_USERS_MAX,
            null,
            null,
            null,
            true,
            $tokenSession
        );

        if (!empty($responseData['errors'])) {
            throw new \DomainException();
        }

        return $responseData['data']['users'];
    }
}
