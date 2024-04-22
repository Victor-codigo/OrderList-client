<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\ListOrdersModify;

use App\Controller\Request\RequestDto;
use App\Form\ListOrders\ListOrdersModify\LIST_ORDERS_MODIFY_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersModify\ListOrdersModifyForm;
use App\Twig\Components\ListOrders\ListOrdersModify\ListOrdersModifyComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/list-orders/modify/{list_orders_name}',
    name: 'list_orders_modify',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ListOrdersModifyController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ListOrdersModifyComponent $listOrdersModifyComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $listOrdersModifyForm = $this->formFactory->create(new ListOrdersModifyForm(), $requestDto->request);

        if ($listOrdersModifyForm->isSubmitted() && $listOrdersModifyForm->isValid()) {
            $this->modifyListOrders($listOrdersModifyForm, $requestDto->groupData->id, $requestDto->listOrdersData->id, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->listOrdersModifyComponent->loadValidationOkTranslation()],
            $this->listOrdersModifyComponent->loadErrorsTranslation($listOrdersModifyForm->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function modifyListOrders(FormInterface $form, string $groupId, string $listOrdersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->listOrdersModify(
            $groupId,
            $listOrdersId,
            $form->getFieldData(LIST_ORDERS_MODIFY_FORM_FIELDS::NAME),
            $form->getFieldData(LIST_ORDERS_MODIFY_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(LIST_ORDERS_MODIFY_FORM_FIELDS::DATE_TO_BUY),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
