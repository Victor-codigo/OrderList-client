<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductCreateAjax;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\ButtonLoading\ButtonLoadingComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductCreateAjaxComponent',
    template: 'Components/Product/ProductCreateAjax/ProductCreateAjaxComponent.html.twig'
)]
final class ProductCreateAjaxComponent extends TwigComponent
{
    public ProductCreateAjaxComponentLangDto $lang;
    public ProductCreateAjaxComponentDto|TwigComponentDtoInterface $data;

    public ButtonLoadingComponentDto $productCreateButtonDto;
    public AlertValidationComponentDto $alertValidationDto;

    public static function getComponentName(): string
    {
        return 'ProductCreateAjaxComponent';
    }

    public function mount(ProductCreateAjaxComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->productCreateButtonDto = $this->createButtonLoadingComponentDto();
        $this->alertValidationDto = $this->createAlertValidationComponentDto();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ProductCreateAjaxComponentLangDto())
            ->backButton(
                $this->translate('product_back_button.label')
            )
            ->productCreateButton(
                $this->translate('product_create_button.label'),
                $this->translate('product_create_button.loading_label'),
            )
            ->build();
    }

    private function createButtonLoadingComponentDto(): ButtonLoadingComponentDto
    {
        return new ButtonLoadingComponentDto(
            null,
            'submit',
            $this->lang->productCreateLabel,
            $this->lang->productCreateLoadingLabel,
            null,
            null
        );
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto([], [], false);
    }
}
