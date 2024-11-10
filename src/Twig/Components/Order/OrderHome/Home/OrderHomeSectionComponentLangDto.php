<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

use Common\Domain\DtoBuilder\DtoBuilder;

class OrderHomeSectionComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $currentBought;
    public readonly string $totalBought;
    public readonly string $buttonShareTitle;
    public readonly string $buttonShareLabel;

    public function __construct(
    ) {
        $this->builder = new DtoBuilder([
            'headerBoughtCounter',
            'buttonShare',
        ]);
    }

    public function headerBoughtCounter(string $currentBought, string $totalBought): self
    {
        $this->builder->setMethodStatus('headerBoughtCounter', true);

        $this->currentBought = $currentBought;
        $this->totalBought = $totalBought;

        return $this;
    }

    public function buttonShare(string $label, string $title): self
    {
        $this->builder->setMethodStatus('buttonShare', true);

        $this->buttonShareLabel = $label;
        $this->buttonShareTitle = $title;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
