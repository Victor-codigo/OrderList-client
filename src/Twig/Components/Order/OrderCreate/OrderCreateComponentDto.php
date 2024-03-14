<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderCreate;

use App\Twig\Components\TwigComponentDtoInterface;

class OrderCreateComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $orderCreateFormActionUrl,
        public readonly string $groupId,
    ) {
    }
}
