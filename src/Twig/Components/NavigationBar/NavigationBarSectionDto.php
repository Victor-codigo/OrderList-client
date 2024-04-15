<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

class NavigationBarSectionDto
{
    public function __construct(
        public readonly string $label,
        public readonly string $title,
        public readonly string $url,
        public readonly bool $active,
    ) {
    }
}
