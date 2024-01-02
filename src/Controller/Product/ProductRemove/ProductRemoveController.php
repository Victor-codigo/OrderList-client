<?php

declare(strict_types=1);

namespace App\Controller\Product\ProductRemove;

use App\Controller\Request\RequestDto;
use App\Form\Product\ProductRemoveMulti\PRODUCT_REMOVE_MULTI_FORM_FIELDS;
use App\Form\Product\ProductRemoveMulti\ProductRemoveMultiForm;
use App\Form\Product\ProductRemove\PRODUCT_REMOVE_FORM_FIELDS;
use App\Form\Product\ProductRemove\ProductRemoveForm;
use App\Twig\Components\Product\ProductRemove\ProductRemoveComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/product/remove',
    name: 'product_remove',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProductRemoveController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ProductRemoveComponent $productRemoveComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);

        $productRemoveForm = $this->productRemoveForm($requestDto);
        $productRemoveMultiForm = $this->productRemoveMultiForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->productRemoveComponent->loadValidationOkTranslation()],
            $this->productRemoveComponent->loadErrorsTranslation(
                array_merge($productRemoveForm->getErrors(), $productRemoveMultiForm->getErrors())
            ),
            []
        );
    }

    private function productRemoveForm(RequestDto $requestDto): FormInterface
    {
        $productRemoveForm = $this->formFactory->create(new ProductRemoveForm(), $requestDto->request);

        if (!$productRemoveForm->isSubmitted() || !$productRemoveForm->isValid()) {
            return $productRemoveForm;
        }

        $this->formValid(
            $productRemoveForm,
            $requestDto->groupData->id,
            $productRemoveForm->getFieldData(PRODUCT_REMOVE_FORM_FIELDS::PRODUCTS_ID) ?? [],
            $requestDto->tokenSession
        );

        return $productRemoveForm;
    }

    private function productRemoveMultiForm(RequestDto $requestDto): FormInterface
    {
        $productRemoveMultiForm = $this->formFactory->create(new ProductRemoveMultiForm(), $requestDto->request);

        if (!$productRemoveMultiForm->isSubmitted() || !$productRemoveMultiForm->isValid()) {
            return $productRemoveMultiForm;
        }

        $this->formValid(
            $productRemoveMultiForm,
            $requestDto->groupData->id,
            $productRemoveMultiForm->getFieldData(PRODUCT_REMOVE_MULTI_FORM_FIELDS::PRODUCTS_ID) ?? [],
            $requestDto->tokenSession
        );

        return $productRemoveMultiForm;
    }

    private function formValid(FormInterface $form, string $groupId, array $productsId, string $tokenSession): void
    {
        $responseData = $this->endpoints->productRemove($groupId, $productsId, null, $tokenSession);

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
