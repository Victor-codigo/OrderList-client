<?php

namespace App\Controller\Shop\ShopHome;

use App\Controller\Request\RequestDto;
use App\Controller\Request\Response\ShopDataResponse;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Form\Shop\ShopModify\ShopModifyForm;
use App\Form\Shop\ShopRemoveMulti\ShopRemoveMultiForm;
use App\Form\Shop\ShopRemove\ShopRemoveForm;
use App\Twig\Components\Shop\ShopHome\ShopHomeComponentDto;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/shop/{group_name}/page-{page}-{page_items}',
    name: 'shop_home',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopHomeController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
        private FlashBagInterface $sessionFlashBag
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);
        $shopModifyForm = $this->formFactory->create(new ShopModifyForm(), $requestDto->request);
        $shopRemoveForm = $this->formFactory->create(new ShopRemoveForm(), $requestDto->request);
        $shopRemoveMultiForm = $this->formFactory->create(new ShopRemoveMultiForm(), $requestDto->request);
        $shopsData = $this->getShopsData($requestDto->groupData->id, $requestDto->tokenSession);

        $shopHomeComponentDto = $this->createShopHomeComponentDto(
            $requestDto,
            $shopCreateForm,
            $shopModifyForm,
            $shopRemoveForm,
            $shopRemoveMultiForm,
            $shopsData
        );

        return $this->renderTemplate($shopHomeComponentDto);
    }

    private function getShopsData(string $groupId, string $tokenSession): array
    {
        $shopsData = $this->endpoints->shopsGetData($groupId, null, null, null, null, $tokenSession);

        return array_map(
            fn (array $shopData) => ShopDataResponse::fromArray($shopData),
            $shopsData['data']
        );
    }

    private function createShopHomeComponentDto(
        RequestDto $requestDto,
        FormInterface $shopCreateForm,
        FormInterface $shopModifyForm,
        FormInterface $shopRemoveForm,
        FormInterface $shopRemoveMultiForm,
        array $shopsData
    ): ShopHomeComponentDto {
        $shopHomeMessagesError = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_ERROR
        );
        $shopHomeMessagesOk = $this->sessionFlashBag->get(
            $requestDto->request->attributes->get('_route').Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_OK
        );

        return (new ShopHomeComponentDto())
            ->errors(
                $shopHomeMessagesOk,
                $shopHomeMessagesError
            )
            ->formCsrfToken(
                $shopCreateForm->getCsrfToken(),
                $shopModifyForm->getCsrfToken(),
                $shopRemoveForm->getCsrfToken(),
                $shopRemoveMultiForm->getCsrfToken(),
            )
            ->pagination(
                $requestDto->page,
                $requestDto->pageItems,
                1
            )
            ->shops(
                $shopsData,
                Config::SHOP_IMAGE_NO_IMAGE_PUBLIC_PATH_200_200,
            )
            ->group(
                $requestDto->groupNameUrlEncoded
            )
            ->form(
                !empty($shopHomeMessagesError) || !empty($shopHomeMessagesOk) ? true : false,
                str_replace(
                    ['{_locale}', '{group_name}'],
                    [$requestDto->locale, $requestDto->groupNameUrlEncoded],
                    Config::CLIENT_ENDPOINT_SHOP_CREATE
                ),
                str_replace(
                    ['{_locale}', '{group_name}'],
                    [$requestDto->locale, $requestDto->groupData->name],
                    Config::CLIENT_ENDPOINT_SHOP_MODIFY
                ),
                str_replace(
                    ['{_locale}', '{group_name}'],
                    [$requestDto->locale, $requestDto->groupData->name],
                    Config::CLIENT_ENDPOINT_SHOP_REMOVE
                )
            )
            ->build();
    }

    private function renderTemplate(ShopHomeComponentDto $shopHomeComponentDto): Response
    {
        return $this->render('shop/shop_home/index.html.twig', [
            'shopHomeComponent' => $shopHomeComponentDto,
        ]);
    }
}
