<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\ItemPriceAdd;

use App\Twig\Components\TwigComponentDtoInterface;
use Common\Domain\DtoBuilder\DtoBuilder;

class ItemPriceAddComponentDto implements TwigComponentDtoInterface
{
    private DtoBuilder $builder;

    public readonly string $itemIdFieldName;
    public readonly string|null $itemIdFieldValue;

    public readonly string $itemNameFieldName;
    public readonly string|null $itemNameFieldValue;
    public readonly string $itemNameLabel;
    public readonly string $itemNamePlaceholder;
    public readonly string $itemNameMsgInvalid;

    public readonly string $itemPriceFieldName;
    public readonly float|null $itemPriceFieldValue;
    public readonly string $itemPriceLabel;
    public readonly string $itemPricePlaceholder;
    public readonly string $itemPriceMsgInvalid;
    public readonly string $itemPriceCurrencyLabel;
    public readonly string $itemPriceUnitMeasureFieldName;

    public readonly string $itemPriceAddButtonLabel;
    public readonly string $itemPriceAddButtonTitle;
    public readonly string $itemPriceAddButtonAlt;

    public readonly string $itemRemoveAddButtonTitle;
    public readonly string $itemRemoveAddButtonAlt;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'itemId',
            'itemName',
            'price',
            'itemPriceAddButton',
            'itemRemoveButton',
        ]);
    }

    public function itemId(string $itemIdFieldName, string|null $itemIdFieldValue): self
    {
        $this->builder->setMethodStatus('itemId', true);

        $this->itemIdFieldName = $itemIdFieldName;
        $this->itemIdFieldValue = $itemIdFieldValue;

        return $this;
    }

    public function itemName(string $itemFieldName, string|null $itemNameFieldValue, string $itemNameLabel, string $itemNamePlaceholder, string $itemNameMsgInvalid): self
    {
        $this->builder->setMethodStatus('itemName', true);

        $this->itemNameFieldName = $itemFieldName;
        $this->itemNameFieldValue = $itemNameFieldValue;
        $this->itemNameLabel = $itemNameLabel;
        $this->itemNamePlaceholder = $itemNamePlaceholder;
        $this->itemNameMsgInvalid = $itemNameMsgInvalid;

        return $this;
    }

    public function price(string $itemPriceFieldName, float|null $itemPriceFieldValue, string $itemPriceLabel, string $itemPricePlaceholder, string $itemPriceMsgInvalid, string $itemPriceCurrencyLabel, string $itemPriceUnitMeasureFieldName): self
    {
        $this->builder->setMethodStatus('price', true);

        $this->itemPriceFieldName = $itemPriceFieldName;
        $this->itemPriceFieldValue = $itemPriceFieldValue;
        $this->itemPriceLabel = $itemPriceLabel;
        $this->itemPricePlaceholder = $itemPricePlaceholder;
        $this->itemPriceMsgInvalid = $itemPriceMsgInvalid;
        $this->itemPriceCurrencyLabel = $itemPriceCurrencyLabel;
        $this->itemPriceUnitMeasureFieldName = $itemPriceUnitMeasureFieldName;

        return $this;
    }

    public function itemPriceAddButton(string $itemPriceAddButtonLabel, string $itemPriceAddButtonTitle, string $itemPriceAddButtonAlt): self
    {
        $this->builder->setMethodStatus('itemPriceAddButton', true);

        $this->itemPriceAddButtonLabel = $itemPriceAddButtonLabel;
        $this->itemPriceAddButtonTitle = $itemPriceAddButtonTitle;
        $this->itemPriceAddButtonAlt = $itemPriceAddButtonAlt;

        return $this;
    }

    public function itemRemoveButton(string $itemRemoveButtonTitle, string $itemRemoveButtonAlt): self
    {
        $this->builder->setMethodStatus('itemRemoveButton', true);

        $this->itemRemoveAddButtonTitle = $itemRemoveButtonTitle;
        $this->itemRemoveAddButtonAlt = $itemRemoveButtonAlt;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
