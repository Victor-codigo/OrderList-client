<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderModify;

use App\Twig\Components\TwigComponentDtoInterface;

class OrderModifyComponentDto implements TwigComponentDtoInterface
{
    public function __construct(
        public readonly array $errors,
        public readonly string $groupId,
        public readonly string $listOrdersId,
        public readonly ?string $name,
        public readonly ?string $description,
        public readonly ?string $csrfToken,
        public readonly bool $validForm,
        public readonly string $formActionUrlPlaceholder,
    ) {
    }
}
