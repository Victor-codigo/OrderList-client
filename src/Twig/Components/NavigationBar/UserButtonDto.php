<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

class UserButtonDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $image,
        public readonly string $title,
        public readonly string $alt,
        public readonly string $profileUrl,
    ) {
    }
}
