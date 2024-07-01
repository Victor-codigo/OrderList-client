<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserPasswordRememberChanged;

use App\Twig\Components\TwigComponentDtoInterface;

class UserPasswordRememberChangedComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $urlLoginForm
    ) {
    }
}
