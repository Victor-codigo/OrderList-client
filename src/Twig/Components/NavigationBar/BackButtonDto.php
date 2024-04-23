<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

class BackButtonDto
{
    public function __construct(
        public readonly string $url,
        public readonly string $title,
    ) {
    }
}
