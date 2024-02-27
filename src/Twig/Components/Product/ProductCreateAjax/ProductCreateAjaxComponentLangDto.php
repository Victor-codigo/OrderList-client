<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductCreateAjax;

use Common\Domain\DtoBuilder\DtoBuilder;

class ProductCreateAjaxComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $backButtonLabel;

    public readonly string $productCreateLabel;
    public readonly string $productCreateLoadingLabel;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'backButton',
            'productCreateButton',
        ]);
    }

    public function backButton(string $backButtonLabel): self
    {
        $this->builder->setMethodStatus('backButton', true);

        $this->backButtonLabel = $backButtonLabel;

        return $this;
    }

    public function productCreateButton(string $productCreateLabel, string $productCreateLoadingLabel): self
    {
        $this->builder->setMethodStatus('productCreateButton', true);

        $this->productCreateLabel = $productCreateLabel;
        $this->productCreateLoadingLabel = $productCreateLoadingLabel;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
