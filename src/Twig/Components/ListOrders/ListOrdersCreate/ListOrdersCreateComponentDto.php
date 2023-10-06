<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersCreate;

use App\Twig\Components\TwigComponentDtoInterface;

class ListOrdersCreateComponentDto implements TwigComponentDtoInterface
{
    /**
     * @param array $userGroups Key = select name; value = value key
     */
    public function __construct(
        public readonly array $errors,
        public readonly string|null $name,
        public readonly string|null $description,
        public readonly \DateTime|null $dateToBuy,
        public readonly string|null $userGroupsSelected,
        public readonly array $userGroups,
        public readonly string|null $csrfToken,
        public readonly bool $validForm,
    ) {
    }
}
