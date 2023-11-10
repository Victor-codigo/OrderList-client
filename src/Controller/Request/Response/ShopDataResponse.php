<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class ShopDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $name,
        public readonly string $description,
        public readonly string|null $image,
        public readonly string $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['id'])
        || !isset($data['group_id'])
        || !isset($data['name'])
        || !isset($data['description'])
        || (null !== $data['image'] && !isset($data['image']))
        || !isset($data['created_on'])) {
            throw new \InvalidArgumentException('Not all shop parameters are provided');
        }

        return new self(
            $data['id'],
            $data['group_id'],
            $data['name'],
            $data['description'],
            $data['image'],
            $data['created_on'],
        );
    }
}
