<?php

namespace App\Twig\Components\HomeSection\HomeList\ListItem;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;

abstract class HomeListItemComponent extends TwigComponent
{
    public HomeListItemComponentLangDto $lang;
    public HomeListItemComponentDto|TwigComponentDtoInterface $data;

    abstract protected function loadTranslation(): void;

    abstract public function mount(HomeListItemComponentDto $data): void;

    abstract public static function getComponentName(): string;
}
