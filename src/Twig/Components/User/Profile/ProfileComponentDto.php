<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Profile;

use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ProfileComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $csrfToken,
        public readonly string|null $email,
        public readonly string|null $nick,
        public readonly string|null $image,
        public readonly ModalComponentDto $emailModal,
        public readonly ModalComponentDto $passwordModal,
        public readonly ModalComponentDto $userRemoveModal,
        public readonly bool $validForm
    ) {
    }
}
