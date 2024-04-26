<?php

declare(strict_types=1);

namespace App\Controller\Group\GroupModify;

use App\Controller\Request\RequestDto;
use App\Form\Group\GroupModify\GROUP_MODIFY_FORM_FIELDS;
use App\Form\Group\GroupModify\GroupModifyForm;
use App\Twig\Components\Group\GroupModify\GroupModifyComponent;
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
    path: '{_locale}/group/modify/{group_id}',
    name: 'group_modify',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class GroupModifyController extends AbstractController
{
    public function __construct(
        private FormFactory $formFactory,
        private HttpClientInterface $httpClient,
        private Endpoints $apiEndpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private GroupModifyComponent $groupModifyComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $groupModifyForm = $this->formFactory->create(new GroupModifyForm(), $requestDto->request);
        $groupId = $requestDto->request->attributes->get('group_id');

        if ($groupModifyForm->isSubmitted() && $groupModifyForm->isValid()) {
            $this->modifyGroup($groupModifyForm, $groupId, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->groupModifyComponent->loadValidationOkTranslation()],
            $this->groupModifyComponent->loadErrorsTranslation($groupModifyForm->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function modifyGroup(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $responseData = $this->apiEndpoints->groupModify(
            $groupId,
            $form->getFieldData(GROUP_MODIFY_FORM_FIELDS::NAME),
            $form->getFieldData(GROUP_MODIFY_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(GROUP_MODIFY_FORM_FIELDS::IMAGE),
            'true' === $form->getFieldData(GROUP_MODIFY_FORM_FIELDS::IMAGE_REMOVE, false) ? true : false,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
