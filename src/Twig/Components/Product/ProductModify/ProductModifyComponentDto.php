<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductModify;

use App\Twig\Components\TwigComponentDtoInterface;

class ProductModifyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string|null $name,
        public readonly string|null $description,
        public readonly string|null $image,
        public readonly string|null $imageNoImage,
        public readonly string|null $csrfToken,
        public readonly bool $validForm,
        public readonly string $formActionUrlPlaceholder,
        public readonly string $shopListSelectModalIdAttribute,
        public readonly string $productModifyModalIdAttribute
    ) {
    }
}
