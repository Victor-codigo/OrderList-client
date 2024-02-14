<?php

declare(strict_types=1);

namespace App\Twig\Components\HomeSection\ItemInfo;

use Common\Domain\DtoBuilder\DtoBuilder;

class ItemInfoComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $itemPriceNameHeader;
    public readonly string $itemPricePriceHeader;
    public readonly string $itemPriceUnitHeader;

    public readonly string $createdOn;
    public readonly string $imageTitle;
    public readonly string $imageAlt;

    public readonly string $closeButtonTitle;
    public readonly string $shopsEmptyMessage;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'priceHeaders',
            'info',
            'shopsEmpty',
            'buttons',
        ]);
    }

    public function priceHeaders(string $itemName, string $itemPrice, string $itemUnit): self
    {
        $this->builder->setMethodStatus('priceHeaders', true);

        $this->itemPriceNameHeader = $itemName;
        $this->itemPricePriceHeader = $itemPrice;
        $this->itemPriceUnitHeader = $itemUnit;

        return $this;
    }

    public function info(string $imageTitle, string $imageAlt, string $createdOn): self
    {
        $this->builder->setMethodStatus('info', true);

        $this->imageTitle = $imageTitle;
        $this->imageAlt = $imageAlt;
        $this->createdOn = $createdOn;

        return $this;
    }

    public function shopsEmpty(string $shopsEmptyMessage): self
    {
        $this->builder->setMethodStatus('shopsEmpty', true);

        $this->shopsEmptyMessage = $shopsEmptyMessage;

        return $this;
    }

    public function buttons(string $closeTitle): self
    {
        $this->builder->setMethodStatus('buttons', true);

        $this->closeButtonTitle = $closeTitle;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
