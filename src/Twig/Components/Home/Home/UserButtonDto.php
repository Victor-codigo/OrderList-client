<?php

declare(strict_types=1);

namespace App\Twig\Components\Home\Home;

class UserButtonDto
{
    public function __construct(
        public readonly string $name,
        public readonly ?string $image,
        public readonly string $title,
        public readonly string $alt,
    ) {
    }
}
