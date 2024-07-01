<?php

declare(strict_types=1);

namespace App\Twig\Components\User\EmailChange;

use App\Twig\Components\TwigComponentDtoInterface;

class EmailChangeComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $email,
        public readonly string $password,
        public readonly string $csrfToken,
        public readonly string $actionAttribute,
        public readonly bool $validForm
    ) {
    }
}
