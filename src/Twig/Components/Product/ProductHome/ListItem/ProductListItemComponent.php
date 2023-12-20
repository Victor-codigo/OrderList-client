<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductListItemComponent',
    template: 'Components/Product/ProductHome/ListItem/ProductListItemComponent.html.twig'
)]
final class ProductListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public ProductListItemComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'ProductListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new ProductListItemComponentLangDto(
            $this->translate('item_modify_button.alt'),
            $this->translate('item_modify_button.title'),
            $this->translate('item_remove_button.alt'),
            $this->translate('item_remove_button.title'),
            $this->translate('item_image.alt'),
            $this->translate('item_image.title'),
        );
    }
}
