<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersCreateFrom;

use App\Twig\Components\TwigComponentDtoInterface;

class ListOrdersCreateFromComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $listOrdersCreateFromFormActionUrl,
    ) {
    }
}
