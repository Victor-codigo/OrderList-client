<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

use Common\Domain\Config\Config;

class ProductDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('group_id', $data)
        || !array_key_exists('name', $data)
        || !array_key_exists('description', $data)
        || !array_key_exists('image', $data)
        || !array_key_exists('created_on', $data)) {
            throw new \InvalidArgumentException('Not all product parameters are provided');
        }

        return new self(
            $data['id'],
            $data['group_id'],
            $data['name'],
            $data['description'],
            null === $data['image'] ? null : Config::API_IMAGES_PRODUCTS_PATH."/{$data['image']}",
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
