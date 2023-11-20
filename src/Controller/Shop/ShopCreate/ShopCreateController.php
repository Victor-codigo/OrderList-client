<?php

namespace App\Controller\Shop\ShopCreate;

use App\Controller\Request\RequestDto;
use App\Form\Shop\ShopCreate\SHOP_CREATE_FORM_FIELDS;
use App\Form\Shop\ShopCreate\ShopCreateForm;
use App\Twig\Components\Shop\ShopCreate\ShopCreateComponent;
use Common\Adapter\Events\Exceptions\RequestRefererException;
use Common\Domain\Config\Config;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\FlashBag\FlashBagInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
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
        private FlashBagInterface $sessionFlashBag,
        private ShopCreateComponent $shopCreateComponent
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $this->validateReferer($requestDto);
        $shopCreateForm = $this->formFactory->create(new ShopCreateForm(), $requestDto->request);

        if ($shopCreateForm->isSubmitted() && $shopCreateForm->isValid()) {
            $this->formValid($shopCreateForm, $requestDto->groupData->id, $requestDto->tokenSession);
        }

        return $this->createRedirectToRoute($requestDto, $shopCreateForm);
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

    private function createRedirectToRoute(RequestDto $requestDto, FormInterface $form): RedirectResponse
    {
        $formErrorsMessages = $this->shopCreateComponent->loadErrorsTranslation($form->getErrors());
        if (empty($formErrorsMessages)) {
            $this->sessionFlashBag->add(
                $form->getFormName().Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_OK,
                $this->shopCreateComponent->loadValidationOkTranslation()
            );
        }

        array_map(
            fn (string $error) => $this->sessionFlashBag->add($form->getFormName().Config::FLASH_BAG_FORM_NAME_SUFFIX_MESSAGE_ERROR, $error),
            $formErrorsMessages
        );

        return $this->redirectToRoute(
            $requestDto->requestReferer->routeName,
            $requestDto->requestReferer->params,
            Response::HTTP_MOVED_PERMANENTLY
        );
    }

    private function validateReferer(RequestDto $requestDto): void
    {
        if (null === $requestDto->requestReferer) {
            throw RequestRefererException::fromMessage('Request referer not valid');
        }
    }
}
