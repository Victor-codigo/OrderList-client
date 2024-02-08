<?php

namespace App\Controller\Product\ProductCreate;

use App\Controller\Request\RequestDto;
use App\Form\Product\ProductCreate\PRODUCT_CREATE_FORM_FIELDS;
use App\Form\Product\ProductCreate\ProductCreateForm;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponent;
use Common\Domain\ControllerUrlRefererRedirect\ControllerUrlRefererRedirect;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/{group_name}/product/create',
    name: 'product_create',
    methods: ['POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProductCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ProductCreateComponent $productCreateComponent,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $productCreateForm = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);

        if ($productCreateForm->isSubmitted() && $productCreateForm->isValid()) {
            $this->formValid($productCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->productCreateComponent->loadValidationOkTranslation()],
            $this->productCreateComponent->loadErrorsTranslation($productCreateForm->getErrors()),
            []
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $tokenSession): void
    {
        try {
            $productId = $this->createProduct($form, $groupId, $tokenSession);
            $this->createProductShopPrice($form, $groupId, $productId, $tokenSession);
        } catch (Error400Exception) {
        }
    }

    /**
     * @return string Product id
     *
     * @throws Error400Exception
     */
    private function createProduct(FormInterface $form, string $groupId, string $tokenSession): string|null
    {
        $responseData = $this->endpoints->productCreate(
            $groupId,
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::NAME),
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::IMAGE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }

        if (!empty($responseData['errors'])) {
            throw new Error400Exception('Product can not be created');
        }

        return $responseData['data']['id'];
    }

    private function createProductShopPrice(FormInterface $form, string $groupId, string $productId, string $tokenSession): void
    {
        $shopsId = array_filter($form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::SHOP_ID));

        if (empty($shopsId)) {
            return;
        }

        $prices = $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::SHOP_PRICE);
        $productsId = array_fill(0, count($shopsId), $productId);

        $responseData = $this->endpoints->setProductShopPrice(
            $groupId,
            $productsId,
            $shopsId,
            $prices,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
