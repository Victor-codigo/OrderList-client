<?php

declare(strict_types=1);

namespace App\Twig\Components\User\Profile;

use App\Twig\Components\Modal\ModalComponentDto;
use App\Twig\Components\TwigComponentDtoInterface;

class ProfileComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $messageErrors,
        public readonly array $messageOk,
        public readonly ?string $csrfToken,
        public readonly ?string $email,
        public readonly ?string $nick,
        public readonly ?string $image,
        public readonly ModalComponentDto $emailModal,
        public readonly ModalComponentDto $passwordModal,
        public readonly ModalComponentDto $userRemoveModal,
        public readonly string $actionAttribute,
        public readonly bool $validForm
    ) {
    }
}
