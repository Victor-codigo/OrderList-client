<?php

declare(strict_types=1);

namespace App\Twig\Components\Order\OrderHome\ListItem;

use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
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
        public readonly float $amount,
        public readonly bool $bought,
        public readonly ?string $image,
        public readonly bool $noImage,
        public readonly \DateTimeImmutable $createdOn,
        public readonly ProductDataResponse $product,
        public readonly ?ShopDataResponse $shop,
        public readonly ?ProductShopPriceDataResponse $productShop,

        public readonly bool $hideInteraction,
    ) {
    }
}
