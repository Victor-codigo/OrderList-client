<?php

namespace App\Controller\Shop\ShopModify;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Shop\ShopModify\SHOP_MODIFY_FORM_FIELDS;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Twig\Components\Shop\ShopModify\ShopModifyComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/shop/{group_name}/modify/{shop_name}',
    name: 'shop_modify',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopModifyController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);

        $validated = false;
        if ($shopModifyForm->isSubmitted() && $shopModifyForm->isValid()) {
            $validated = true;
            $this->formValid($shopModifyForm, $requestDto->groupData->id, $requestDto->shopData->id, $requestDto->tokenSession);

            if (!$shopModifyForm->hasErrors()) {
                return $this->redirectToRoute('shop_modify', [
                    'group_name' => $this->endpoints->encodeUrl($requestDto->groupData->name),
                    'shop_name' => $this->endpoints->encodeUrl($shopModifyForm->getFieldData(SHOP_MODIFY_FORM_FIELDS::NAME)),
                ]);
            }
        }

        return $this->renderTemplate(
            $this->createShopModifyComponent($shopModifyForm, $requestDto->shopData, $validated)
        );
    }

    private function formValid(FormInterface $form, string $groupId, string $shopId, string $tokenSession): void
    {
        $responseData = $this->endpoints->shopModify(
            $shopId,
            $groupId,
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::NAME),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::DESCRIPTION),
            $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::IMAGE),
            'true' === $form->getFieldData(SHOP_MODIFY_FORM_FIELDS::IMAGE_REMOVE) ? true : false,
            $tokenSession
        );

        foreach ($responseData['errors'] as $error => $errorDescription) {
            $form->addError($error, $errorDescription);
        }
    }

    private function createShopModifyComponent(FormInterface $form, ShopDataResponse $shopData, bool $validated): ShopModifyComponentDto
    {
        return new ShopModifyComponentDto(
            $form->getErrors(),
            $shopData->name,
            $shopData->description,
            null === $shopData->image
                ? Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200
                : Config::API_IMAGES_SHOP_PATH."/{$shopData->image}",
            Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            $form->getCsrfToken(),
            $validated
        );
    }

    private function renderTemplate(ShopModifyComponentDto $shopModifyComponentDto): Response
    {
        return $this->render('shop/shop_modify/index.html.twig', [
            'shopModifyComponent' => $shopModifyComponentDto,
        ]);
    }
}
