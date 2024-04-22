<?php

declare(strict_types=1);

namespace App\Controller\Order\OrderRemove;

use App\Controller\Request\RequestDto;
use App\Form\Order\OrderRemoveMulti\ORDER_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Order\OrderRemoveMulti\OrderRemoveMultiForm;
use App\Form\Order\OrderRemove\ORDER_REMOVE_FORM_FIELDS;
use App\Form\Order\OrderRemove\OrderRemoveForm;
use App\Twig\Components\Order\OrderRemove\OrderRemoveComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/order/remove',
    name: 'order_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class OrderRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private OrderRemoveComponent $orderRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $orderRemoveForm = $this->orderRemoveForm($requestDto);
        $orderRemoveMultiForm = $this->orderRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->orderRemoveComponent->loadValidationOkTranslation()],
            $this->orderRemoveComponent->loadErrorsTranslation(
                array_merge($orderRemoveForm->getErrors(), $orderRemoveMultiForm->getErrors())
            ),
            []
        );
    }

    private function orderRemoveForm(RequestDto $requestDto): FormInterface
    {
        $orderRemoveForm = $this->formFactory->create(new OrderRemoveForm(), $requestDto->request);

        if (!$orderRemoveForm->isSubmitted() || !$orderRemoveForm->isValid()) {
            return $orderRemoveForm;
        }

        $this->formValid(
            $orderRemoveForm,
            $requestDto->groupData->id,
            $orderRemoveForm->getFieldData(ORDER_REMOVE_FORM_FIELDS::ORDERS_ID) ?? [],
            $requestDto->getTokenSessionOrFail()
        );

        return $orderRemoveForm;
    }

    private function orderRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $orderRemoveMultiForm = $this->formFactory->create(new OrderRemoveMultiForm(), $requestDto->request);

        if (!$orderRemoveMultiForm->isSubmitted() || !$orderRemoveMultiForm->isValid()) {
            return $orderRemoveMultiForm;
        }

        $this->formValid(
            $orderRemoveMultiForm,
            $requestDto->groupData->id,
            $orderRemoveMultiForm->getFieldData(ORDER_REMOVE_MULTI_FORM_FIELDS::ORDERS_ID) ?? [],
            $requestDto->getTokenSessionOrFail()
        );

        return $orderRemoveMultiForm;
    }

    private function formValid(FormInterface $form, string $groupId, array $ordersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->ordersRemove($groupId, $ordersId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
