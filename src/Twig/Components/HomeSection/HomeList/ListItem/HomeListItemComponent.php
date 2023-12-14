<?php

namespace App\Twig\Components\HomeSection\HomeList\ListItem;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomeListItemComponent',
    template: 'Components/HomeSection/HomeList/ListItem/HomeListItemComponent.html.twig'
)]
final class HomeListItemComponent extends TwigComponent
{
    public HomeListItemComponentLangDto $lang;
    public HomeListItemComponentDto|TwigComponentDtoInterface $data;

    public static function getComponentName(): string
    {
        return 'HomeListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new HomeListItemComponentLangDto(
            $this->translate('item_image.alt'),
            $this->translate('item_image.title'),
            $this->translate('item_modify_button.alt'),
            $this->translate('item_modify_button.title'),
            $this->translate('item_remove_button.alt'),
            $this->translate('item_remove_button.title'),
        );
    }
}
