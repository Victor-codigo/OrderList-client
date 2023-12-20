<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopListItemComponent',
    template: 'Components/Shop/ShopHome/ListItem/ShopListItemComponent.html.twig'
)]
final class ShopListItemComponent extends HomeListItemComponent
{
    public static function getComponentName(): string
    {
        return 'ShopListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new ShopListItemComponentLangDto(
            $this->translate('shop_modify_button.alt'),
            $this->translate('shop_modify_button.title'),
            $this->translate('shop_remove_button.alt'),
            $this->translate('shop_remove_button.title'),
            $this->translate('shop_image.alt'),
            $this->translate('shop_image.title'),
        );
    }
}
