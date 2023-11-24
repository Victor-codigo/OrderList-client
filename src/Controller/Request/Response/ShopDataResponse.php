<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

use Common\Domain\Config\Config;

class ShopDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $name,
        public readonly string|null $description,
        public readonly string|null $image,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])
        || !isset($data['group_id'])
        || !isset($data['name'])
        || !array_key_exists('description', $data)
        || !array_key_exists('image', $data)
        || !isset($data['created_on'])) {
            throw new \InvalidArgumentException('Not all shop parameters are provided');
        }

        return new self(
            $data['id'],
            $data['group_id'],
            $data['name'],
            $data['description'],
            null === $data['image'] ? null : Config::API_IMAGES_SHOP_PATH."/{$data['image']}",
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
