<?php

declare(strict_types=1);

namespace App\Controller\Notification\NotificationRemove;

use App\Controller\Request\RequestDto;
use App\Form\Notification\NotificationRemove\NOTIFICATION_REMOVE_FORM_FIELDS;
use App\Form\Notification\NotificationRemove\NotificationRemoveForm;
use App\Twig\Components\Notification\NotificationRemove\NotificationRemoveComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/notifications/remove',
    name: 'notification_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class NotificationRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private NotificationRemoveComponent $notificationRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $notificationRemoveForm = $this->notificationRemoveForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->notificationRemoveComponent->loadValidationOkTranslation()],
            $this->notificationRemoveComponent->loadErrorsTranslation(
                $notificationRemoveForm->getErrors()
            ),
            []
        );
    }

    private function notificationRemoveForm(RequestDto $requestDto): FormInterface
    {
        $notificationRemoveForm = $this->formFactory->create(new NotificationRemoveForm(), $requestDto->request);

        if (!$notificationRemoveForm->isSubmitted() || !$notificationRemoveForm->isValid()) {
            return $notificationRemoveForm;
        }

        $this->formValid(
            $notificationRemoveForm,
            $notificationRemoveForm->getFieldData(NOTIFICATION_REMOVE_FORM_FIELDS::NOTIFICATIONS_ID, []),
            $requestDto->getTokenSessionOrFail()
        );

        return $notificationRemoveForm;
    }

    private function formValid(FormInterface $form, array $notificationsId, string $tokenSession): void
    {
        $responseData = $this->endpoints->notificationRemove($notificationsId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
