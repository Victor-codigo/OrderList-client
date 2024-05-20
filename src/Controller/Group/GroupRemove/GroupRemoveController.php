<?php

declare(strict_types=1);

namespace App\Controller\Group\GroupRemove;

use App\Controller\Request\RequestDto;
use App\Form\Group\GroupRemove\GROUP_REMOVE_FORM_FIELDS;
use App\Form\Group\GroupRemove\GroupRemoveForm;
use App\Form\Group\GroupRemoveMulti\GROUP_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Group\GroupRemoveMulti\GroupRemoveMultiForm;
use App\Twig\Components\Group\GroupRemove\GroupRemoveComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/group/remove',
    name: 'group_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class GroupRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupRemoveComponent $groupRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $groupRemoveForm = $this->groupRemoveForm($requestDto);
        $groupRemoveMultiForm = $this->groupRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupRemoveComponent->loadValidationOkTranslation()],
            $this->groupRemoveComponent->loadErrorsTranslation(
                array_merge($groupRemoveForm->getErrors(), $groupRemoveMultiForm->getErrors())
            ),
            []
        );
    }

    private function groupRemoveForm(RequestDto $requestDto): FormInterface
    {
        $groupRemoveForm = $this->formFactory->create(new GroupRemoveForm(), $requestDto->request);

        if (!$groupRemoveForm->isSubmitted() || !$groupRemoveForm->isValid()) {
            return $groupRemoveForm;
        }

        $this->requestGroupRemove(
            $groupRemoveForm,
            $groupRemoveForm->getFieldData(GROUP_REMOVE_FORM_FIELDS::GROUPS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupRemoveForm;
    }

    private function groupRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $groupRemoveMultiForm = $this->formFactory->create(new GroupRemoveMultiForm(), $requestDto->request);

        if (!$groupRemoveMultiForm->isSubmitted() || !$groupRemoveMultiForm->isValid()) {
            return $groupRemoveMultiForm;
        }

        $this->requestGroupRemove(
            $groupRemoveMultiForm,
            $groupRemoveMultiForm->getFieldData(GROUP_REMOVE_MULTI_FORM_FIELDS::GROUPS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $groupRemoveMultiForm;
    }

    private function requestGroupRemove(FormInterface $form, array $groupsId, string $tokenSession): void
    {
        $responseData = $this->endpoints->groupRemove($groupsId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
