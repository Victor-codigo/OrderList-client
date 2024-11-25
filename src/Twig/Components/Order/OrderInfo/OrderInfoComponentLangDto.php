<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderInfo;

use App\Twig\Components\HomeSection\ItemInfo\ItemInfoComponentLangDto;

class OrderInfoComponentLangDto extends ItemInfoComponentLangDto
{
    public readonly string $amount;

    public readonly string $bought;
    public readonly string $boughtTitle;
    public readonly string $notBoughtTitle;

    public readonly string $shopPrice;
    public readonly string $priceTotal;

    public function __construct()
    {
        parent::__construct();

        $this->builder->addBuilderMethod('amount');
        $this->builder->addBuilderMethod('bought');
        $this->builder->addBuilderMethod('shop');
        $this->builder->addBuilderMethod('price');
    }

    public function amount(string $amount): self
    {
        $this->builder->setMethodStatus('amount', true);

        $this->amount = $amount;

        return $this;
    }

    public function bought(string $bought, string $boughtTitle, string $notBoughtTitle): self
    {
        $this->builder->setMethodStatus('bought', true);

        $this->bought = $bought;
        $this->boughtTitle = $boughtTitle;
        $this->notBoughtTitle = $notBoughtTitle;

        return $this;
    }

    public function shop(string $price): self
    {
        $this->builder->setMethodStatus('shop', true);

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
