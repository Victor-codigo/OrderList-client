<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class NotificationDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $type,
        public readonly string $userId,
        public readonly string $message,
        public readonly array $data,
        public readonly bool $viewed,
        public readonly \DateTimeImmutable $createdOn,
    ) {
    }

    public static function fromArray(array $data): self
    {
        if (!array_key_exists('id', $data)
        || !array_key_exists('type', $data)
        || !array_key_exists('user_id', $data)
        || !array_key_exists('message', $data)
        || !array_key_exists('data', $data)
        || !array_key_exists('viewed', $data)
        || !array_key_exists('created_on', $data)) {
            throw new \InvalidArgumentException('Not all notification parameters are provided');
        }

        return new self(
            $data['id'],
            $data['type'],
            $data['user_id'],
            $data['message'],
            $data['data'],
            $data['viewed'],
            \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $data['created_on']),
        );
    }
}
