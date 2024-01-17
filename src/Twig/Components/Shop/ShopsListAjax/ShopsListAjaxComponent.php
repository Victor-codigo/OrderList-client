<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopsListAjax;

use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopsListAjaxComponent',
    template: 'Components/Shop/ShopsListAjax/ShopsListAjaxComponent.html.twig'
)]
class ShopsListAjaxComponent extends TwigComponent
{
    public ShopsListAjaxComponentLangDto $lang;
    public ShopsListAjaxComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'ShopsListAjaxComponent';
    }

    public function mount(ShopsListAjaxComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->titleDto = $this->createTitle();
    }

    private function createTitle(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    private function loadTranslation(): void
    {
        $this->lang = new ShopsListAjaxComponentLangDto(
            $this->translate('title'),
            $this->translate('shop_image.title'),
            $this->translate('button_back.label'),
            $this->translate('button_create_shop.label'),
        );
    }
}
