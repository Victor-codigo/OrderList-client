<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class UserDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $name,
        public readonly array $roles,
        public readonly ?string $image,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('email', $data)
        || !array_key_exists('name', $data)
        || !array_key_exists('roles', $data)
        || !array_key_exists('image', $data)
        || !array_key_exists('created_on', $data)) {
            throw new \InvalidArgumentException('Not all list orders parameters are provided');
        }

        return new self(
            $data['id'],
            $data['email'],
            $data['name'],
            $data['roles'],
            $data['image'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
