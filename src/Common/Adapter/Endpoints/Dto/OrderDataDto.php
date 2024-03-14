<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints\Dto;

class OrderDataDto
{
    public function __construct(
        public readonly string $productId,
        public readonly ?string $shopId,
        public readonly ?string $description,
        public readonly ?float $amount,
    ) {
    }
}
