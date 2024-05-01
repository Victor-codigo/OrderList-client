<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class GroupUserDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly ?string $image,
        public readonly bool $admin,
        public readonly \DateTimeImmutable $createdOn
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('name', $data)
        || !array_key_exists('image', $data)
        || !array_key_exists('admin', $data)
        || !array_key_exists('created_on', $data)) {
            throw new \InvalidArgumentException('Not all list orders parameters are provided');
        }

        return new self(
            $data['id'],
            $data['name'],
            $data['image'],
            $data['admin'],
            null === $data['created_on'] ? null : \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
