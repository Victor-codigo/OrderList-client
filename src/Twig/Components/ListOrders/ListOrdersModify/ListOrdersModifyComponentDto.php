<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersModify;

use App\Twig\Components\TwigComponentDtoInterface;

class ListOrdersModifyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?\DateTime $dateToBuy,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $listOrdersModifyFormActionUrl,
    ) {
    }
}
