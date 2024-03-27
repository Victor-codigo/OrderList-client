<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class OrderDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $listOrdersId,
        public readonly string $productId,
        public readonly ?string $shopId,
        public readonly string $userId,
        public readonly ?string $description,
        public readonly ?float $amount,
        public readonly bool $bought,
        public readonly \DateTimeImmutable $createdOn,
        public readonly ProductDataResponse $product,
        public readonly ?ShopDataResponse $shop,
        public readonly ?ProductShopPriceDataResponse $productShop,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('group_id', $data)
        || !array_key_exists('list_orders_id', $data)
        || !array_key_exists('user_id', $data)
        || !array_key_exists('description', $data)
        || !array_key_exists('amount', $data)
        || !array_key_exists('bought', $data)
        || !array_key_exists('created_on', $data)
        || !array_key_exists('product', $data)
        || !array_key_exists('shop', $data)
        || !array_key_exists('productShop', $data)) {
            throw new \InvalidArgumentException(sprintf('[%s]: Not all order parameters are provided', self::class));
        }

        $data['product_id'] = $data['product']['id'];

        $data['shop_id'] = null;
        if (!empty($data['shop'])) {
            $data['shop_id'] = $data['shop']['id'];
        }

        return new self(
            $data['id'],
            $data['group_id'],
            $data['list_orders_id'],
            $data['product_id'],
            $data['shop_id'],
            $data['user_id'],
            $data['description'],
            $data['amount'],
            $data['bought'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
            self::createProductDataResponse($data),
            self::createShopDataResponse($data),
            self::createProductShopDataResponse($data)
        );
    }

    private static function createProductDataResponse(array $data): ProductDataResponse
    {
        if (!array_key_exists('id', $data['product'])
        || !array_key_exists('name', $data['product'])
        || !array_key_exists('description', $data['product'])
        || !array_key_exists('image', $data['product'])
        || !array_key_exists('created_on', $data['product'])) {
            throw new \InvalidArgumentException(sprintf('[%s]: Not all product parameters are provided', self::class));
        }

        $data['product']['group_id'] = $data['group_id'];

        return ProductDataResponse::fromArray($data['product']);
    }

    private static function createShopDataResponse(array $data): ?ShopDataResponse
    {
        if (empty($data['shop'])) {
            return null;
        }

        if (!array_key_exists('id', $data['shop'])
        || !array_key_exists('name', $data['shop'])
        || !array_key_exists('description', $data['shop'])
        || !array_key_exists('image', $data['shop'])
        || !array_key_exists('created_on', $data['shop'])) {
            throw new \InvalidArgumentException(sprintf('[%s]: Not all shop parameters are provided', self::class));
        }

        $data['shop']['group_id'] = $data['group_id'];

        return ShopDataResponse::fromArray($data['shop']);
    }

    private static function createProductShopDataResponse(array $data): ?ProductShopPriceDataResponse
    {
        if (empty($data['productShop'])) {
            return null;
        }

        if (!array_key_exists('price', $data['productShop'])
        || !array_key_exists('unit', $data['productShop'])) {
            throw new \InvalidArgumentException(sprintf('[%s]: Not all productShop parameters are provided', self::class));
        }

        $data['productShop']['product_id'] = $data['product']['id'];
        $data['productShop']['shop_id'] = $data['shop']['id'];

        return ProductShopPriceDataResponse::fromArray($data['productShop']);
    }
}
