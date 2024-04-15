<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

use Common\Domain\DtoBuilder\DtoBuilder;

class NavigationBarLangDto
{
    private DtoBuilder $builder;

    public readonly string $title;
    private array $sections;

    public function __construct()
    {
        $this->builder = new DtoBuilder([
            'title',
            'addSection',
        ]);
    }

    public function getSections(): array
    {
        return $this->sections;
    }

    public function build(): static
    {
        $this->builder->validate();

        return $this;
    }

    public function title(string $title): static
    {
        $this->builder->setMethodStatus('title', true);

        $this->title = $title;

        return $this;
    }

    public function addSection(string $section): static
    {
        $this->builder->setMethodStatus('addSection', true);

        $this->sections[] = $section;

        return $this;
    }
}
