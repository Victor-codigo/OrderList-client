<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;

class OrderInfoComponentLangDto extends ItemInfoComponentLangDto
{
    public readonly string $amount;

    public readonly string $bought;
    public readonly string $boughtTitle;
    public readonly string $boughtAlt;
    public readonly string $notBoughtTitle;
    public readonly string $notBoughtAlt;

    public readonly string $productDescriptionTitle;

    public readonly string $shopDescriptionTitle;

    public readonly string $shopPrice;
    public readonly string $priceTotal;

    public function __construct()
    {
        parent::__construct();

        $this->builder->addBuilderMethod('amount');
        $this->builder->addBuilderMethod('bought');
        $this->builder->addBuilderMethod('product');
        $this->builder->addBuilderMethod('shop');
        $this->builder->addBuilderMethod('price');
    }

    public function amount(string $amount): self
    {
        $this->builder->setMethodStatus('amount', true);

        $this->amount = $amount;

        return $this;
    }

    public function bought(string $bought, string $boughtTitle, string $boughtAlt, string $notBoughtTitle, string $notBoughtAlt): self
    {
        $this->builder->setMethodStatus('bought', true);

        $this->bought = $bought;
        $this->boughtTitle = $boughtTitle;
        $this->boughtAlt = $boughtAlt;
        $this->notBoughtTitle = $notBoughtTitle;
        $this->notBoughtAlt = $notBoughtAlt;

        return $this;
    }

    public function product(string $descriptionTitle): self
    {
        $this->builder->setMethodStatus('product', true);

        $this->productDescriptionTitle = $descriptionTitle;

        return $this;
    }

    public function shop(string $descriptionTitle, string $price): self
    {
        $this->builder->setMethodStatus('shop', true);

        $this->shopDescriptionTitle = $descriptionTitle;
        $this->shopPrice = $price;

        return $this;
    }

    public function price(string $priceTotal): self
    {
        $this->builder->setMethodStatus('price', true);

        $this->priceTotal = $priceTotal;

        return $this;
    }
}
