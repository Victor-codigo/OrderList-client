<?php

declare(strict_types=1);

namespace App\Controller\Order\OrderCreate;

use App\Controller\Request\RequestDto;
use App\Form\Order\OrderCreate\ORDER_CREATE_FORM_FIELDS;
use App\Form\Order\OrderCreate\OrderCreateForm;
use App\Twig\Components\Order\OrderCreate\OrderCreateComponent;
use Common\Adapter\Endpoints\Dto\OrderDataDto;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/order/create',
    name: 'order_create',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class OrderCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private OrderCreateComponent $orderCreateComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $orderCreateForm = $this->formFactory->create(new OrderCreateForm(), $requestDto->request);

        if ($orderCreateForm->isSubmitted() && $orderCreateForm->isValid()) {
            $this->validForm($orderCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->orderCreateComponent->loadValidationOkTranslation()],
            $this->orderCreateComponent->loadErrorsTranslation($orderCreateForm->getErrors()),
            []
        );
    }

    /**
     * @return string Order id
     *
     * @throws Error400Exception
     */
    private function validForm(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $orderData = new OrderDataDto(
            $form->getFieldData(ORDER_CREATE_FORM_FIELDS::PRODUCT_ID, ''),
            $form->getFieldData(ORDER_CREATE_FORM_FIELDS::SHOP_ID),
            $form->getFieldData(ORDER_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(ORDER_CREATE_FORM_FIELDS::AMOUNT),
        );

        $responseData = $this->endpoints->ordersCreate(
            $groupId,
            $form->getFieldData(ORDER_CREATE_FORM_FIELDS::LIST_ORDERS_ID, ''),
            [$orderData],
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError((string) $error, '');
        }
    }
}
