<?php

declare(strict_types=1);

namespace App\Controller\Request\Response;

class GroupDataResponse
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string|null $image,
        public readonly string $createdOn,
    ) {
    }
}
