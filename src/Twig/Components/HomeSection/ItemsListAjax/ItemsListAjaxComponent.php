<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemsListAjax;

use App\Twig\Components\Controls\Title\TitleComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemsListAjaxComponent',
    template: 'Components/HomeSection/ItemsListAjax/ItemsListAjaxComponent.html.twig'
)]
abstract class ItemsListAjaxComponent extends TwigComponent
{
    abstract protected function loadTranslation(): void;

    public ItemsListAjaxComponentLangDto $lang;
    public ItemsListAjaxComponentDto|TwigComponentDtoInterface $data;

    public readonly TitleComponentDto $titleDto;

    public static function getComponentName(): string
    {
        return 'ItemsListAjaxComponent';
    }

    public function mount(ItemsListAjaxComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();
        $this->titleDto = $this->createTitle();
    }

    private function createTitle(): TitleComponentDto
    {
        return new TitleComponentDto($this->lang->title);
    }

    // private function loadTranslation(): void
    // {
    //     $this->lang = new ItemsListAjaxComponentLangDto(
    //         $this->translate('title'),
    //         $this->translate('shop_image.title'),
    //         $this->translate('button_back.label'),
    //         $this->translate('button_create_shop.label'),
    //     );
    // }
}
