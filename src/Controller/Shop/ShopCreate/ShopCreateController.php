<?php

namespace App\Controller\Shop\ShopCreate;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponentDto;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/shop/{group_name}/create',
    name: 'shop_create',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ShopCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
    ) {
    }

    public function __invoke2(RequestDto $requestDto): Response
    {
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);

        $validated = false;
        if ($shopCreateForm->isSubmitted() && $shopCreateForm->isValid()) {
            $validated = true;
            $this->formValid($shopCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);

            if (!$shopCreateForm->hasErrors()) {
                return $this->redirectToRoute('shop_create', [
                    'group_name' => $this->endpoints->encodeUrl($requestDto->groupName),
                ]);
            }
        }

        return $this->renderTemplate(
            $this->createShopCreateComponent($shopCreateForm, $validated)
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

    private function createShopCreateComponent(FormInterface $form, bool $validated): ShopCreateComponentDto
    {
        $formSaved = $validated && empty($form->getErrors());

        return new ShopCreateComponentDto(
            $form->getErrors(),
            $formSaved ? null : $form->getFieldData(SHOP_CREATE_FORM_FIELDS::NAME),
            $formSaved ? null : $form->getFieldData(SHOP_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getCsrfToken(),
            $validated
        );
    }

    private function renderTemplate(ShopCreateComponentDto $shopCreateComponentDto): Response
    {
        return $this->render('shop/shop_create/index.html.twig', [
            'shopCreateComponent' => $shopCreateComponentDto,
        ]);
    }
}
