<?php

namespace App\Controller\Shop\ShopRemove;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopRemoveMulti\ShopRemoveMultiForm;
use App\Form\Shop\ShopRemove\SHOP_REMOVE_FORM_FIELDS;
use App\Form\Shop\ShopRemove\ShopRemoveForm;
use App\Twig\Components\Shop\ShopRemove\ShopRemoveComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/shop/{group_name}/remove',
    name: 'shop_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ShopRemoveComponent $shopRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $shopRemoveForm = $this->shopRemoveForm($requestDto);
        $shopRemoveMultiForm = $this->shopRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->shopRemoveComponent->loadValidationOkTranslation()],
            $this->shopRemoveComponent->loadErrorsTranslation(
                array_merge($shopRemoveForm->getErrors(), $shopRemoveMultiForm->getErrors())
            )
        );
    }

    private function shopRemoveForm(RequestDto $requestDto): FormInterface
    {
        $shopRemoveForm = $this->formFactory->create(new ShopRemoveForm(), $requestDto->request);

        if (!$shopRemoveForm->isSubmitted() || !$shopRemoveForm->isValid()) {
            return $shopRemoveForm;
        }

        $this->formValid(
            $shopRemoveForm,
            $requestDto->groupData->id,
            $shopRemoveForm->getFieldData(SHOP_REMOVE_FORM_FIELDS::SHOPS_ID) ?? [],
            $requestDto->tokenSession
        );

        return $shopRemoveForm;
    }

    private function shopRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $shopRemoveMultiForm = $this->formFactory->create(new ShopRemoveMultiForm(), $requestDto->request);

        if (!$shopRemoveMultiForm->isSubmitted() || !$shopRemoveMultiForm->isValid()) {
            return $shopRemoveMultiForm;
        }

        $this->formValid(
            $shopRemoveMultiForm,
            $requestDto->groupData->id,
            $shopRemoveMultiForm->getFieldData(SHOP_REMOVE_FORM_FIELDS::SHOPS_ID) ?? [],
            $requestDto->tokenSession
        );

        return $shopRemoveMultiForm;
    }

    private function formValid(FormInterface $form, string $groupId, array $shopsId, string $tokenSession): void
    {
        $responseData = $this->endpoints->shopRemove(
            $groupId,
            $shopsId,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}