<?php

namespace App\Controller\Shop\ShopModify;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopModify\SHOP_MODIFY_FORM_FIELDS;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponent;
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
    path: '{_locale}/{group_name}/shop/modify/{shop_name}',
    name: 'shop_modify',
    methods: ['POST'],
    requirements: [
        '_locale' => Config::CLIENT_DOMAIN_LOCALE_VALID,
    ]
)]
class ShopModifyController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private ShopModifyComponent $shopModifyComponent,
        private ControllerUrlRefererRedirect $controllerUrlRefererRedirect
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->controllerUrlRefererRedirect->validateReferer($requestDto->requestReferer);
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);

        if ($shopModifyForm->isSubmitted() && $shopModifyForm->isValid()) {
            $this->formValid($shopModifyForm, $requestDto->groupData->id, $requestDto->getShopData()->id, $requestDto->getTokenSessionOrFail());
        }

        return $this->controllerUrlRefererRedirect->createRedirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            [$this->shopModifyComponent->loadValidationOkTranslation()],
            $this->shopModifyComponent->loadErrorsTranslation($shopModifyForm->getErrors()),
            []
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $shopId, string $tokenSession): void
    {
        try {
            $shopId = $this->modifyShop($form, $groupId, $shopId, $tokenSession);
            $this->createShopShopPrice($form, $groupId, $shopId, $tokenSession);
        } catch (Error400Exception $th) {
        }
    }

    private function modifyShop(FormInterface $form, string $groupId, string $shopId, string $tokenSession): ?string
    {
        $responseData = $this->endpoints->shopModify(
            $shopId,
            $groupId,
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::NAME),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::ADDRESS),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::IMAGE),
            'true' === $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::IMAGE_REMOVE) ? true : false,
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

    private function createShopShopPrice(FormInterface $form, string $groupId, string $shopId, string $tokenSession): void
    {
        $productsId = array_filter($form->getFieldData(SHOP_MODIFY_FORM_FIELDS::PRODUCT_ID, []));

        if (empty($productsId)) {
            return;
        }

        $responseData = $this->endpoints->setProductShopPrice(
            $groupId,
            null,
            $shopId,
            $productsId,
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::PRODUCT_PRICE),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::PRODUCT_UNIT_MEASURE),
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }
}
