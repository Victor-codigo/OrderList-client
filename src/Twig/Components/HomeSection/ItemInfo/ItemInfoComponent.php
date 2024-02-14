<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemInfo;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemInfoComponent',
    template: 'Components/HomeSection/ItemInfo/ItemInfoComponent.html.twig'
)]
abstract class ItemInfoComponent extends TwigComponent
{
    public readonly ItemInfoComponentLangDto $lang;
    public ItemInfoComponentDto|TwigComponentDtoInterface $data;

    public string $componentName;

    abstract public function mount(ItemInfoComponentDto $data): void;

    abstract public static function getComponentName(): string;

    abstract protected function loadTranslation(): void;
}
