<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\LIstOrdersCreate;

use App\Controller\Request\RequestDto;
use App\Form\ListOrders\ListOrdersCreate\LIST_ORDERS_CREATE_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersCreate\ListOrdersCreateForm;
use App\Twig\Components\ListOrders\ListOrdersCreate\ListOrdersCreateComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/list-orders/create',
    name: 'list_orders_create',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class listOrdersCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ListOrdersCreateComponent $listOrdersCreateComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $listOrdersCreate = $this->formFactory->create(new ListOrdersCreateForm(), $requestDto->request);

        if ($listOrdersCreate->isSubmitted() && $listOrdersCreate->isValid()) {
            $this->createListOrders($listOrdersCreate, $requestDto->groupData->id, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->listOrdersCreateComponent->loadValidationOkTranslation()],
            $this->listOrdersCreateComponent->loadErrorsTranslation($listOrdersCreate->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function createListOrders(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $responseData = $this->endpoints->listOrdersCreate(
            $groupId,
            $form->getFieldData(LIST_ORDERS_CREATE_FORM_FIELDS::NAME),
            $form->getFieldData(LIST_ORDERS_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(LIST_ORDERS_CREATE_FORM_FIELDS::DATE_TO_BUY),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
