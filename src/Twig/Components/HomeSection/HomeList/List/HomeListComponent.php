<?php

namespace App\Twig\Components\HomeSection\HomeList\List;

use App\Twig\Components\List\ListComponentDto;
use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'HomeListComponent',
    template: 'Components/HomeSection/HomeList/List/HomeListComponent.html.twig'
)]
final class HomeListComponent extends TwigComponent
{
    public HomeListComponentLangDto $lang;
    public HomeListComponentDto|TwigComponentDtoInterface $data;
    public readonly ListComponentDto $homeListDto;

    public static function getComponentName(): string
    {
        return 'HomeListComponent';
    }

    public function mount(HomeListComponentDto $data): void
    {
        $this->data = $data;

        $this->loadTranslation();

        $this->homeListDto = $this->createListComponentDto();
    }

    private function createListComponentDto(): ListComponentDto
    {
        return new ListComponentDto(
            $this->data->listItemComponentName,
            $this->data->listItems,
            $this->lang->homeListEmptyIconAlt,
            $this->lang->homeListEmptyMessage
        );
    }

    private function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new HomeListComponentLangDto(
            $this->translate('home_list_empty.message'),
            $this->translate('home_list_empty.icon.alt'),
        );
    }
}
