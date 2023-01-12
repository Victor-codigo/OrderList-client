<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

class NavigationBarLangDto
{
    public readonly string $title;
    private array $sections;

    public function getSections(): array
    {
        return $this->sections;
    }

    public function build(): static
    {
        return $this;
    }

    public function title(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function addSection(string $section): static
    {
        $this->sections[] = $section;

        return $this;
    }
}
