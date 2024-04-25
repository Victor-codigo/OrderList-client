<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class GroupDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?string $image,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!isset($data['group_id'])
        || !isset($data['name'])
        || !array_key_exists('description', $data)
        || !array_key_exists('image', $data)
        || !isset($data['created_on'])
        ) {
            throw new \InvalidArgumentException('Not all group parameters are provided');
        }

        return new self(
            $data['group_id'],
            $data['name'],
            $data['description'],
            $data['image'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
