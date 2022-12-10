<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Login;

use App\Form\Login\LOGIN_FORM_FIELDS;
use App\Twig\Components\TwigComponentDtoInterface;

class LoginComponentDto implements TwigComponentDtoInterface
{
    public readonly LOGIN_FORM_FIELDS $fields;

    public function __construct(
        public readonly array $errors,
        public readonly string|null $email,
        public readonly string|null $password,
        public readonly bool|null $rememberMe,
        public readonly string|null $csrfToken,
    ) {
        $this->fields = new LOGIN_FORM_FIELDS();
    }
}
