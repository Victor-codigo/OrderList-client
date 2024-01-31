<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductCreate;

use App\Twig\Components\TwigComponentDtoInterface;

class ProductCreateComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $name,
        public readonly float|null $price,
        public readonly string|null $description,
        public readonly string|null $csrfToken,
        public readonly bool $validForm,
        public readonly string $productCreateFormActionUrl,
        public readonly string $shopListSelectModalIdAttribute
    ) {
    }
}
