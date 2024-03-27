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
use Common\Domain\Response\RESPONSE_STATUS;
use Common\Domain\Response\ResponseDto;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
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

    #[Route(
        path: '{_locale}/{group_name}/product/create',
        name: 'product_create',
    )]
    public function createProductHttp(RequestDto $requestDto): Response
    {
        $responseData = $this->productCreateForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->productCreateComponent->loadValidationOkTranslation()],
            $this->productCreateComponent->loadErrorsTranslation($responseData->getErrors()),
            []
        );
    }

    #[Route(
        path: 'ajax/{_locale}/{group_name}/product/create',
        name: 'product_create_ajax',
    )]
    public function createProductAjax(RequestDto $requestDto): Response
    {
        $responseData = $this->productCreateForm($requestDto);

        return new JsonResponse(
            $responseData->toArray(),
            RESPONSE_STATUS::OK === $responseData->getStatus() ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }

    private function productCreateForm(RequestDto $requestDto): ResponseDto
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $productCreateForm = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);

        if ($productCreateForm->isSubmitted() && $productCreateForm->isValid()) {
            return $this->productCreateRequest($productCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return new ResponseDto(
            [],
            [],
            'Form not allowed',
            RESPONSE_STATUS::OK
        );
    }

    private function productCreateRequest(FormInterface $form, string $groupId, string $tokenSession): ResponseDto
    {
        try {
            $productId = $this->createProduct($form, $groupId, $tokenSession);
            $this->createProductShopPrice($form, $groupId, $productId, $tokenSession);

            $responseData = ['id' => $productId];
            $responseStatus = RESPONSE_STATUS::OK;
            $responseMessage = 'Product created';
            if (!empty($form->getErrors())) {
                $responseStatus = RESPONSE_STATUS::ERROR;
                $responseMessage = 'Product could not be created';
            }
        } catch (Error400Exception) {
            $responseData = [];
            $responseStatus = RESPONSE_STATUS::ERROR;
            $responseMessage = 'Product could not be created';
        } finally {
            $responseErrors = $this->productCreateComponent->loadErrorsTranslation($form->getErrors());

            return new ResponseDto(
                $responseData,
                $responseErrors,
                $responseMessage,
                $responseStatus
            );
        }
    }

    /**
     * @return string Product id
     *
     * @throws Error400Exception
     */
    private function createProduct(FormInterface $form, string $groupId, string $tokenSession): string
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
        $shopsId = array_filter($form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::SHOP_ID, []));

        if (empty($shopsId)) {
            return;
        }

        $responseData = $this->endpoints->setProductShopPrice(
            $groupId,
            $productId,
            null,
            $shopsId,
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::SHOP_PRICE),
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::SHOP_UNIT_MEASURE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
