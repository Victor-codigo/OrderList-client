<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\Home;

use Common\Domain\DtoBuilder\DtoBuilder;

class OrderHomeSectionComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $currentBought;
    public readonly string $totalBought;
    public readonly string $buttonShareWhatsAppTitle;
    public readonly string $buttonShareWhatsAppLabel;

    public function __construct(
    ) {
        $this->builder = new DtoBuilder([
            'headerBoughtCounter',
            'buttonShareWhatsApp',
        ]);
    }

    public function headerBoughtCounter(string $currentBought, string $totalBought): self
    {
        $this->builder->setMethodStatus('headerBoughtCounter', true);

        $this->currentBought = $currentBought;
        $this->totalBought = $totalBought;

        return $this;
    }

    public function buttonShareWhatsApp(string $label, string $title): self
    {
        $this->builder->setMethodStatus('buttonShareWhatsApp', true);

        $this->buttonShareWhatsAppLabel = $label;
        $this->buttonShareWhatsAppTitle = $title;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
