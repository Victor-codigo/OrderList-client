<?php

declare(strict_types=1);

namespace App\Controller\ListOrders\ListOrdersRemove;

use App\Controller\Request\RequestDto;
use App\Form\ListOrders\ListOrdersRemove\LIST_ORDERS_REMOVE_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersRemove\ListOrdersRemoveForm;
use App\Form\ListOrders\ListOrdersRemoveMulti\LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS;
use App\Form\ListOrders\ListOrdersRemoveMulti\ListOrdersRemoveMultiForm;
use App\Twig\Components\ListOrders\ListOrdersRemove\ListOrdersRemoveComponent;
use Common\Domain\Config\Config;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/list-orders/remove',
    name: 'list_orders_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class ListOrdersRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ListOrdersRemoveComponent $listOrdersRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $listOrdersRemoveForm = $this->listOrdersRemoveForm($requestDto);
        $listOrdersRemoveMultiForm = $this->listOrdersRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->listOrdersRemoveComponent->loadValidationOkTranslation()],
            $this->listOrdersRemoveComponent->loadErrorsTranslation(
                array_merge($listOrdersRemoveForm->getErrors(), $listOrdersRemoveMultiForm->getErrors())
            ),
            []
        );
    }

    private function listOrdersRemoveForm(RequestDto $requestDto): FormInterface
    {
        $listOrdersRemoveForm = $this->formFactory->create(new ListOrdersRemoveForm(), $requestDto->request);

        if (!$listOrdersRemoveForm->isSubmitted() || !$listOrdersRemoveForm->isValid()) {
            return $listOrdersRemoveForm;
        }

        $this->formValid(
            $listOrdersRemoveForm,
            $requestDto->groupData->id,
            $listOrdersRemoveForm->getFieldData(LIST_ORDERS_REMOVE_FORM_FIELDS::LIST_ORDERS_ID) ?? [],
            $requestDto->getTokenSessionOrFail()
        );

        return $listOrdersRemoveForm;
    }

    private function listOrdersRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $listOrdersRemoveMultiForm = $this->formFactory->create(new ListOrdersRemoveMultiForm(), $requestDto->request);

        if (!$listOrdersRemoveMultiForm->isSubmitted() || !$listOrdersRemoveMultiForm->isValid()) {
            return $listOrdersRemoveMultiForm;
        }

        $this->formValid(
            $listOrdersRemoveMultiForm,
            $requestDto->groupData->id,
            $listOrdersRemoveMultiForm->getFieldData(LIST_ORDERS_REMOVE_MULTI_FORM_FIELDS::LIST_ORDERS_ID) ?? [],
            $requestDto->getTokenSessionOrFail()
        );

        return $listOrdersRemoveMultiForm;
    }

    private function formValid(FormInterface $form, string $groupId, array $listsOrdersId, string $tokenSession): void
    {
        try {
            $this->requestListOrdersRemove($form, $groupId, $listsOrdersId, $tokenSession);
            $this->requestListOrdersRemoveOrders($form, $groupId, $listsOrdersId, $tokenSession);
        } catch (Error400Exception) {
        }
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @throws Error400Exception
     */
    private function requestListOrdersRemove(FormInterface $form, string $groupId, array $listsOrdersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->listOrdersRemove($groupId, $listsOrdersId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }

        if (!empty($form->getErrors())) {
            throw new Error400Exception('List orders could not be removed');
        }
    }

    private function requestListOrdersRemoveOrders(FormInterface $form, string $groupId, array $listsOrdersId, string $tokenSession): void
    {
        $responseData = $this->endpoints->listOrdersRemoveOrders($groupId, $listsOrdersId, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
