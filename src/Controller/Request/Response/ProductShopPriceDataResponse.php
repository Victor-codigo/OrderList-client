<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class ProductShopPriceDataResponse
{
    public function __construct(
        public readonly string $productId,
        public readonly string $shopId,
        public readonly float|null $price,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('product_id', $data)
        || !array_key_exists('shop_id', $data)
        || !array_key_exists('price', $data)) {
            throw new \InvalidArgumentException('Not all product shop parameters are provided');
        }

        return new self(
            $data['product_id'],
            $data['shop_id'],
            $data['price']
        );
    }
}
