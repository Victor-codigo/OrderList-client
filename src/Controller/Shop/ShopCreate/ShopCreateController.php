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
use Common\Domain\Response\RESPONSE_STATUS;
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
        ['form' => $shopCreateForm] = $this->shopCreateForm($requestDto);

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->shopCreateComponent->loadValidationOkTranslation()],
            $this->shopCreateComponent->loadErrorsTranslation($shopCreateForm->getErrors()),
            []
        );
    }

    #[Route(
        path: 'ajax/{_locale}/{group_name}/shop/create',
        name: 'shop_create_ajax',
    )]
    public function createShopAjax(RequestDto $requestDto): Response
    {
        ['form' => $shopCreateForm, 'responseData' => $responseData] = $this->shopCreateForm($requestDto);
        $responseData['errors'] = $this->shopCreateComponent->loadErrorsTranslation(
            $shopCreateForm->getErrors()
        );

        return new JsonResponse(
            $responseData,
            RESPONSE_STATUS::OK === $responseData['status'] ? Response::HTTP_CREATED : Response::HTTP_OK
        );
    }

    /**
     * @return array<{
     *  form: FormInterface,
     *  responseData: array
     * }>
     */
    public function shopCreateForm(RequestDto $requestDto): array
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);
        $shopCreatedResponseData = null;

        if ($shopCreateForm->isSubmitted() && $shopCreateForm->isValid()) {
            $shopCreatedResponseData = $this->shopCreateRequest($shopCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return [
            'form' => $shopCreateForm,
            'responseData' => $shopCreatedResponseData,
        ];
    }

    private function shopCreateRequest(FormInterface $form, string $groupId, string $tokenSession): array
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

        return $responseData;
    }
}
