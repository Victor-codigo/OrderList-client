<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopCreate;

use App\Twig\Components\TwigComponentDtoInterface;

class ShopCreateComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $name,
        public readonly ?string $address,
        public readonly ?string $description,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $shopCreateFormActionUrl,
    ) {
    }
}
