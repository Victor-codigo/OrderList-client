<?php

declare(strict_types=1);

namespace App\Controller\Order\OrderModify;

use App\Controller\Request\RequestDto;
use App\Form\Order\OrderModify\ORDER_MODIFY_FORM_FIELDS;
use App\Form\Order\OrderModify\OrderModifyForm;
use App\Twig\Components\Order\OrderModify\OrderModifyComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/order/modify/{order_name}',
    name: 'order_modify',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class OrderModifyController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private OrderModifyComponent $orderModifyComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $orderModifyForm = $this->formFactory->create(new OrderModifyForm(), $requestDto->request);

        if ($orderModifyForm->isSubmitted() && $orderModifyForm->isValid()) {
            $this->formValid($orderModifyForm, $requestDto->groupData->id, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->orderModifyComponent->loadValidationOkTranslation()],
            $this->orderModifyComponent->loadErrorsTranslation($orderModifyForm->getErrors()),
            []
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $responseData = $this->endpoints->orderModify(
            $groupId,
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::LIST_ORDERS_ID, ''),
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::ORDER_ID, ''),
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::PRODUCT_ID, ''),
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::SHOP_ID),
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(ORDER_MODIFY_FORM_FIELDS::AMOUNT),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
