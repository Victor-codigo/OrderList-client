<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemInfo;

use Common\Domain\DtoBuilder\DtoBuilder;

class ItemInfoComponentLangDto
{
    protected DtoBuilder $builder;

    public readonly string $itemPriceNameHeader;
    public readonly string $itemPricePriceHeader;
    public readonly string $itemPriceUnitHeader;

    public readonly string $createdOn;
    public readonly string $imageTitle;
    public readonly string $imageAlt;

    public readonly string $description;

    public readonly string $closeButtonTitle;
    public readonly string $shopsEmptyMessage;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'priceHeaders',
            'info',
            'description',
            'shopsEmpty',
            'buttons',
        ]);
    }

    public function priceHeaders(string $itemName, string $itemPrice, string $itemUnit): static
    {
        $this->builder->setMethodStatus('priceHeaders', true);

        $this->itemPriceNameHeader = $itemName;
        $this->itemPricePriceHeader = $itemPrice;
        $this->itemPriceUnitHeader = $itemUnit;

        return $this;
    }

    public function info(string $imageTitle, string $imageAlt, string $createdOn): static
    {
        $this->builder->setMethodStatus('info', true);

        $this->imageTitle = $imageTitle;
        $this->imageAlt = $imageAlt;
        $this->createdOn = $createdOn;

        return $this;
    }

    public function shopsEmpty(string $shopsEmptyMessage): static
    {
        $this->builder->setMethodStatus('shopsEmpty', true);

        $this->shopsEmptyMessage = $shopsEmptyMessage;

        return $this;
    }

    public function description(string $descriptionText): static
    {
        $this->builder->setMethodStatus('description', true);

        $this->description = $descriptionText;

        return $this;
    }

    public function buttons(string $closeTitle): static
    {
        $this->builder->setMethodStatus('buttons', true);

        $this->closeButtonTitle = $closeTitle;

        return $this;
    }

    public function build(): static
    {
        $this->builder->validate();

        return $this;
    }
}
