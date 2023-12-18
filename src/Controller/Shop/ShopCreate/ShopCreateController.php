<?php

namespace App\Controller\Shop\ShopCreate;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/shop/create',
    name: 'shop_create',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ShopCreateComponent $shopCreateComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);

        if ($shopCreateForm->isSubmitted() && $shopCreateForm->isValid()) {
            $this->formValid($shopCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->shopCreateComponent->loadValidationOkTranslation()],
            $this->shopCreateComponent->loadErrorsTranslation($shopCreateForm->getErrors()),
            []
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $tokenSession): void
    {
        $responseData = $this->endpoints->shopCreate(
            $groupId,
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::NAME),
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::IMAGE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
