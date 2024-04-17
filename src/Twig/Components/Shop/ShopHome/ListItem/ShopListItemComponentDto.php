<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;

class ShopListItemComponentDto extends HomeListItemComponentDto
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
        public readonly string $name,
        public readonly string $modifyFormModalIdAttribute,
        public readonly string $deleteFormModalIdAttribute,
        public readonly string $infoFormModalIdAttribute,
        public readonly string $translationDomainName,

        public readonly ?string $description,
        public readonly ?string $image,
        public readonly bool $noImage,
        public readonly \DateTimeImmutable $createdOn,

        public readonly array $products,
        public readonly array $productsShopsPrice,
    ) {
    }
}
