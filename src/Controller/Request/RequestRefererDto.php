<?php

declare(strict_types=1);

namespace App\Controller\Request;

class RequestRefererDto
{
    public function __construct(
        public readonly string $routeName,
        public readonly array $params,
    ) {
    }
}
