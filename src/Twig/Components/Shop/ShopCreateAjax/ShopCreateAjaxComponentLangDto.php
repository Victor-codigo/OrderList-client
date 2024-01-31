<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreateAjax;

use Common\Domain\DtoBuilder\DtoBuilder;

class ShopCreateAjaxComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $backButtonLabel;

    public readonly string $shopCreateLabel;
    public readonly string $shopCreateLoadingLabel;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'backButton',
            'shopCreateButton',
        ]);
    }

    public function backButton(string $backButtonLabel): self
    {
        $this->builder->setMethodStatus('backButton', true);

        $this->backButtonLabel = $backButtonLabel;

        return $this;
    }

    public function shopCreateButton(string $shopCreateLabel, string $shopCreateLoadingLabel): self
    {
        $this->builder->setMethodStatus('shopCreateButton', true);

        $this->shopCreateLabel = $shopCreateLabel;
        $this->shopCreateLoadingLabel = $shopCreateLoadingLabel;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
