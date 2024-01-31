<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreateAjax;

use App\Twig\Components\AlertValidation\AlertValidationComponentDto;
use App\Twig\Components\Controls\ButtonLoading\ButtonLoadingComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopCreateAjaxComponent',
    template: 'Components/Shop/ShopCreateAjax/ShopCreateAjaxComponent.html.twig'
)]
final class ShopCreateAjaxComponent extends TwigComponent
{
    public ShopCreateAjaxComponentLangDto $lang;
    public ShopCreateAjaxComponentDto|TwigComponentDtoInterface $data;

    public ButtonLoadingComponentDto $shopCreateButtonDto;
    public AlertValidationComponentDto $alertValidationDto;

    public static function getComponentName(): string
    {
        return 'ShopCreateAjaxComponent';
    }

    public function mount(ShopCreateAjaxComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->shopCreateButtonDto = $this->createButtonLoadingComponentDto();
        $this->alertValidationDto = $this->createAlertValidationComponentDto();
    }

    private function loadTranslation(): void
    {
        $this->lang = (new ShopCreateAjaxComponentLangDto())
            ->backButton(
                $this->translate('shop_back_button.label')
            )
            ->shopCreateButton(
                $this->translate('shop_create_button.label'),
                $this->translate('shop_create_button.loading_label'),
            )
            ->build();
    }

    private function createButtonLoadingComponentDto(): ButtonLoadingComponentDto
    {
        return new ButtonLoadingComponentDto(
            'submit',
            $this->lang->shopCreateLabel,
            $this->lang->shopCreateLoadingLabel
        );
    }

    private function createAlertValidationComponentDto(): AlertValidationComponentDto
    {
        return new AlertValidationComponentDto([], [], false);
    }
}
