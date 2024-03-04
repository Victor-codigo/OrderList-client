<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class ListOrdersListItemComponentDto extends HomeListItemComponentDto
{
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $name,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $infoFormModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $groupId,
        public readonly ?string $userId,
        public readonly ?string $description,
        public readonly ?\DateTimeImmutable $dateToBuy,
        public readonly \DateTimeImmutable $createdOn,
        public readonly ?string $image,
    ) {
    }
}
