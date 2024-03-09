<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class OrderDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $userId,
        public readonly ?string $description,
        public readonly ?float $amount,
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
        || !array_key_exists('user_id', $data)
        || !array_key_exists('description', $data)
        || !array_key_exists('amount', $data)
        || !array_key_exists('created_on', $data)
        || !array_key_exists('product', $data)
        || !array_key_exists('shop', $data)
        || !array_key_exists('productShop', $data)
        ) {
            throw new \InvalidArgumentException('Not all order parameters are provided');
        }

        $data['product']['group_id'] = $data['group_id'];

        return new self(
            $data['id'],
            $data['group_id'],
            $data['user_id'],
            $data['description'],
            $data['amount'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
            ProductDataResponse::fromArray($data['product']),
            empty($data['shop']) ? null : ShopDataResponse::fromArray($data['shop']),
            empty($data['productShop']) ? null : ProductShopPriceDataResponse::fromArray($data['productShop'])
        );
    }
}
