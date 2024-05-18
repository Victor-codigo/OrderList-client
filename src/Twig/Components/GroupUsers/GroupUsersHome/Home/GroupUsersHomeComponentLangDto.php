<?php

declare(strict_types=1);

namespace App\Twig\Components\GroupUsers\GroupUsersHome\Home;

use Common\Domain\DtoBuilder\DtoBuilder;

class GroupUsersHomeComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $infoModalTitle;
    public readonly string $infoModalText;
    public readonly string $infoModalCloseButtonLabel;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'infoModal',
        ]);
    }

    public function infoModal(string $title, string $text, string $buttonLabel): self
    {
        $this->builder->setMethodStatus('infoModal', true);

        $this->infoModalTitle = $title;
        $this->infoModalText = $text;
        $this->infoModalCloseButtonLabel = $buttonLabel;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
