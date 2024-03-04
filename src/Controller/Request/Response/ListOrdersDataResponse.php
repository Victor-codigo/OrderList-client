<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class ListOrdersDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $groupId,
        public readonly string $userId,
        public readonly string $name,
        public readonly ?string $description,
        public readonly ?\DateTimeImmutable $dateToBuy,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('group_id', $data)
        || !array_key_exists('user_id', $data)
        || !array_key_exists('name', $data)
        || !array_key_exists('description', $data)
        || !array_key_exists('date_to_buy', $data)
        || !array_key_exists('created_on', $data)) {
            throw new \InvalidArgumentException('Not all list orders parameters are provided');
        }

        return new self(
            $data['id'],
            $data['group_id'],
            $data['user_id'],
            $data['name'],
            $data['description'],
            null === $data['date_to_buy']
                ? null
                : \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['date_to_buy']),
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
