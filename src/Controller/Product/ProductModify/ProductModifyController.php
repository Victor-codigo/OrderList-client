<?php

declare(strict_types=1);

namespace App\Controller\Product\ProductModify;

use App\Controller\Request\RequestDto;
use App\Form\Product\ProductModify\PRODUCT_MODIFY_FORM_FIELDS;
use App\Form\Product\ProductModify\ProductModifyForm;
use App\Twig\Components\Product\ProductModify\ProductModifyComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/product/modify/{product_name}',
    name: 'product_modify',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProductModifyController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ProductModifyComponent $productModifyComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $productModifyForm = $this->formFactory->create(new ProductModifyForm(), $requestDto->request);

        if ($productModifyForm->isSubmitted() && $productModifyForm->isValid()) {
            $this->formValid($productModifyForm, $requestDto->groupData->id, $requestDto->productData->id, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->productModifyComponent->loadValidationOkTranslation()],
            $this->productModifyComponent->loadErrorsTranslation($productModifyForm->getErrors()),
            []
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $productId, string $tokenSession): void
    {
        try {
            $productId = $this->modifyProduct($form, $groupId, $productId, $tokenSession);
            $this->modifyProductShopPrice($form, $groupId, $productId, $tokenSession);
        } catch (Error400Exception $th) {
        }
    }

    private function modifyProduct(FormInterface $form, string $groupId, string $productId, string $tokenSession): string|null
    {
        $responseData = $this->endpoints->productModify(
            $groupId,
            $productId,
            null,
            $form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::NAME),
            $form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::DESCRIPTION),
            null,
            $form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::IMAGE),
            'true' === $form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::IMAGE_REMOVE) ? true : false,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }

        if (!empty($responseData['errors'])) {
            throw new Error400Exception('Product can not be modify');
        }

        return $responseData['data']['id'];
    }

    private function modifyProductShopPrice(FormInterface $form, string $groupId, string $productId, string $tokenSession): void
    {
        $shopsId = array_filter($form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::SHOP_ID, []));
        $prices = [];

        if (!empty($shopsId)) {
            $prices = $form->getFieldData(PRODUCT_MODIFY_FORM_FIELDS::SHOP_PRICE);
        }

        $responseData = $this->endpoints->setProductShopPrice(
            $groupId,
            $productId,
            null,
            $shopsId,
            $prices,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
