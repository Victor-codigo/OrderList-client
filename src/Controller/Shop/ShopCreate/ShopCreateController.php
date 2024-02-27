<?php

namespace App\Controller\Shop\ShopCreate;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
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
class ShopCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect,
        private ShopCreateComponent $shopCreateComponent
    ) {
    }

    #[Route(
        path: '{_locale}/{group_name}/shop/create',
        name: 'shop_create'
    )]
    public function createShopHttp(RequestDto $requestDto): Response
    {
        $responseData = $this->shopCreateForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->shopCreateComponent->loadValidationOkTranslation()],
            $responseData->getErrors(),
            []
        );
    }

    #[Route(
        path: 'ajax/{_locale}/{group_name}/shop/create',
        name: 'shop_create_ajax',
    )]
    public function createShopAjax(RequestDto $requestDto): Response
    {
        $responseData = $this->shopCreateForm($requestDto);

        return new JsonResponse(
            $responseData,
            RESPONSE_STATUS::OK === $responseData->getStatus() ? Response::HTTP_CREATED : Response::HTTP_BAD_REQUEST
        );
    }

    public function shopCreateForm(RequestDto $requestDto): ResponseDto
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);

        if ($shopCreateForm->isSubmitted() && $shopCreateForm->isValid()) {
            return $this->shopCreateRequest($shopCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return new ResponseDto(
            [],
            [],
            'Form not allowed',
            RESPONSE_STATUS::OK
        );
    }

    private function shopCreateRequest(FormInterface $form, string $groupId, string $tokenSession): ResponseDto
    {
        try {
            $productId = $this->createShop($form, $groupId, $tokenSession);
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
            $responseErrors = $this->shopCreateComponent->loadErrorsTranslation($form->getErrors());

            return new ResponseDto(
                $responseData,
                $responseErrors,
                $responseMessage,
                $responseStatus
            );
        }
    }

    /**
     * @return string Shop id
     *
     * @throws Error400Exception
     */
    private function createShop(FormInterface $form, string $groupId, string $tokenSession): string
    {
        $responseData = $this->endpoints->shopCreate(
            $groupId,
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::NAME, ''),
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::IMAGE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }

        if (!empty($responseData['errors'])) {
            throw new Error400Exception('Shop can not be created');
        }

        return $responseData['data']['id'];
    }

    private function createProductShopPrice(FormInterface $form, string $groupId, string $shopId, string $tokenSession): void
    {
        $productsId = array_filter($form->getFieldData(SHOP_CREATE_FORM_FIELDS::PRODUCT_ID, []));

        if (empty($productsId)) {
            return;
        }

        $responseData = $this->endpoints->setProductShopPrice(
            $groupId,
            null,
            $shopId,
            $productsId,
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::PRODUCT_PRICE),
            $form->getFieldData(SHOP_CREATE_FORM_FIELDS::PRODUCT_UNIT_MEASURE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
