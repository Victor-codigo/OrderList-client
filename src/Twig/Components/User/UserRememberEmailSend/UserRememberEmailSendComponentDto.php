<?php

declare(strict_types=1);

namespace App\Twig\Components\User\UserRememberEmailSend;

use App\Twig\Components\TwigComponentDtoInterface;

class UserRememberEmailSendComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly string $urlRememberPasswordForm
    ) {
    }
}
