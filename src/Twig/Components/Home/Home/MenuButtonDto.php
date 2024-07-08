<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

class MenuButtonDto
{
    public function __construct(
        public readonly string $label,
        public readonly string $title,
        public readonly string $url,
        public readonly ?string $image,
    ) {
    }
}
