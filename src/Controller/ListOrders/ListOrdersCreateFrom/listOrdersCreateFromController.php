<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\ListOrdersCreateFrom;

use App\Controller\Request\RequestDto;
use App\Form\ListOrders\ListOrdersCreateFrom\LIST_ORDERS_CREATE_FROM_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersCreateFrom\ListOrdersCreateFromForm;
use App\Twig\Components\ListOrders\ListOrdersCreateFrom\ListOrdersCreateFromComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/list-orders/create-from',
    name: 'list_orders_create_from',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class listOrdersCreateFromController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ListOrdersCreateFromComponent $listOrdersCreateFromComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $listOrdersCreateForm = $this->formFactory->create(new ListOrdersCreateFromForm(), $requestDto->request);

        if ($listOrdersCreateForm->isSubmitted() && $listOrdersCreateForm->isValid()) {
            $this->createListOrdersFrom($listOrdersCreateForm, $requestDto->groupData->id, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->listOrdersCreateFromComponent->loadValidationOkTranslation()],
            $this->listOrdersCreateFromComponent->loadErrorsTranslation($listOrdersCreateForm->getErrors()),
            []
        );
    }

    /**
     * @return string listOrders id
     *
     * @throws Error400Exception
     */
    private function createListOrdersFrom(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $responseData = $this->endpoints->listOrdersCreateFrom(
            $groupId,
            $form->getFieldData(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::LIST_ORDERS_CREATE_FROM_ID),
            $form->getFieldData(LIST_ORDERS_CREATE_FROM_FORM_FIELDS::NAME),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
