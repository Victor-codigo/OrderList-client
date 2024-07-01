<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Tryout;

class TryoutComponentLangDto
{
    public function __construct(
        private readonly string $pageTitle,
        private readonly string $redirectingText,
    ) {
    }
}
