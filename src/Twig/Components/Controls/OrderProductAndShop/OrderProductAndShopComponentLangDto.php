<?php

declare(strict_types=1);

namespace App\Twig\Components\Controls\OrderProductAndShop;

use Common\Domain\DtoBuilder\DtoBuilder;

class OrderProductAndShopComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $productLabel;
    public readonly string $productPlaceholder;
    public readonly string $productMsgInvalid;

    public readonly string $productButtonTitle;
    public readonly string $productButtonAlt;
    public readonly string $productButtonLabel;

    public readonly string $shopLabel;
    public readonly string $shopMsgInvalid;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'product',
            'productSelectButton',
            'shop',
        ]);
    }

    public function product(string $label, string $placeholder, string $msgError): self
    {
        $this->builder->setMethodStatus('product', true);

        $this->productLabel = $label;
        $this->productPlaceholder = $placeholder;
        $this->productMsgInvalid = $msgError;

        return $this;
    }

    public function productSelectButton(string $label, string $title, string $alt): self
    {
        $this->builder->setMethodStatus('productSelectButton', true);

        $this->productButtonLabel = $label;
        $this->productButtonTitle = $title;
        $this->productButtonAlt = $alt;

        return $this;
    }

    public function shop(string $label, string $msgError): self
    {
        $this->builder->setMethodStatus('shop', true);

        $this->shopLabel = $label;
        $this->shopMsgInvalid = $msgError;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
