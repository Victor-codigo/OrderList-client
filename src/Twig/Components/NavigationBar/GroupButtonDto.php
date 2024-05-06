<?php

declare(strict_types=1);

namespace App\Twig\Components\NavigationBar;

class GroupButtonDto
{
    public function __construct(
        public readonly string $name,
        public readonly string $title,
        public readonly string $groupsUrl,
    ) {
    }
}
