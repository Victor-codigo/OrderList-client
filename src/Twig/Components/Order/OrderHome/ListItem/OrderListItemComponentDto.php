<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class OrderListItemComponentDto extends HomeListItemComponentDto
{
    /**
     * @param array<{
     *      id: string,
     *      name: string,
     *      description: string,
     *      image: string,
     *      price; float|null
     * }> $shops
     */
    public function __construct(
        public readonly string $componentName,
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $name,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $orderInfoModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $description,
        public readonly ?string $image,
        public readonly \DateTimeImmutable $createdOn,
        public readonly array $product,
        public readonly ?array $shop,
    ) {
    }
}
