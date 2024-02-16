<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ItemPriceAdd;

use App\Twig\Components\TwigComponent;
use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\Config\UNIT_MEASURE;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ItemPriceAddComponent',
    template: 'Components/Controls/ItemPriceAdd/ItemPriceAddComponent.htmL.twig'
)]
class ItemPriceAddComponent extends TwigComponent
{
    public readonly ItemPriceAddComponentLangDto $lang;
    public ItemPriceAddComponentDto|TwigComponentDtoInterface $data;

    /**
     * @var string[]
     */
    public readonly array $unitsMeasureValue;

    public static function getComponentName(): string
    {
        return 'ItemPriceAddComponent';
    }

    public function mount(ItemPriceAddComponentDto $data): void
    {
        $this->data = $data;
        $this->unitsMeasureValue = $this->getUnitsMeasureNames();
        $this->loadTranslation();
    }

    private function loadTranslation(): void
    {
        $this->lang = new ItemPriceAddComponentLangDto(
            $this->translateUnitsMeasure()
        );
    }

    /**
     * @return string[]
     */
    private function translateUnitsMeasure(): array
    {
        return array_map(
            fn (UNIT_MEASURE $unit) => match ($unit) {
                UNIT_MEASURE::UNITS => $this->translate('unit.unit'),
                default => $unit->value,
            },
            UNIT_MEASURE::cases()
        );
    }

    private function getUnitsMeasureNames(): array
    {
        return array_map(
            fn (UNIT_MEASURE $unit) => $unit->name,
            UNIT_MEASURE::cases()
        );
    }
}
