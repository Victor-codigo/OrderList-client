<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\Home;

use Common\Domain\DtoBuilder\DtoBuilder;

class ListOrdersHomeSectionComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $buttonCreateFromLabel;
    public readonly string $buttonCreateFromTitle;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'buttonCreateFrom',
        ]);
    }

    public function buttonCreateFrom(string $label, string $title): self
    {
        $this->builder->setMethodStatus('buttonCreateFrom', true);

        $this->buttonCreateFromLabel = $label;
        $this->buttonCreateFromTitle = $title;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
