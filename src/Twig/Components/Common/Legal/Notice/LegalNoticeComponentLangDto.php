<?php

declare(strict_types=1);

namespace App\Twig\Components\Common\Legal\Notice;

use Common\Domain\DtoBuilder\DtoBuilder;

class LegalNoticeComponentLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
        ]);
    }

    public function title(string $title): self
    {
        $this->builder->setMethodStatus('title', true);

        $this->title = $title;

        return $this;
    }

    public function build(): self
    {
        $this->builder->validate();

        return $this;
    }
}
