<?php

namespace App\Controller\Product\ProductCreate;

use App\Controller\Request\RequestDto;
use App\Form\Product\ProductCreate\PRODUCT_CREATE_FORM_FIELDS;
use App\Form\Product\ProductCreate\ProductCreateForm;
use App\Twig\Components\Product\ProductCreate\ProductCreateComponentDto;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\Form\FormFactoryInterface;
use Common\Domain\Ports\Form\FormInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(
    path: '{_locale}/product/{group_name}/create',
    name: 'product_create',
    methods: ['GET', 'POST'],
    requirements: [
        '_locale' => 'en|es',
    ]
)]
class ProductCreateController extends AbstractController
{
    public function __construct(
        private FormFactoryInterface $formFactory,
        private EndpointsInterface $endpoints,
    ) {
    }

    public function __invoke(RequestDto $requestDto): Response
    {
        $productCreateForm = $this->formFactory->create(new ProductCreateForm(), $requestDto->request);

        $submitted = false;
        if ($productCreateForm->isSubmitted() && $productCreateForm->isValid()) {
            $submitted = true;
            $this->formValid($productCreateForm, $requestDto->groupData['group_id'], $requestDto->tokenSession);
        }

        $productCreateComponent = $this->createProductCreateComponent($productCreateForm, $submitted);

        return $this->renderTemplate($productCreateComponent);
    }

    private function formValid(FormInterface $form, string $groupId, string $tokenSession): void
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
    }

    private function createProductCreateComponent(FormInterface $form, bool $validate): ProductCreateComponentDto
    {
        return new ProductCreateComponentDto(
            $form->getErrors(),
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::NAME),
            $form->getFieldData(PRODUCT_CREATE_FORM_FIELDS::DESCRIPTION),
            $form->getCsrfToken(),
            $validate
        );
    }

    private function renderTemplate(ProductCreateComponentDto $ProductCreateComponentDto): Response
    {
        return $this->render('product/product_create/index.html.twig', [
            'ProductCreateComponent' => $ProductCreateComponentDto,
        ]);
    }
}
