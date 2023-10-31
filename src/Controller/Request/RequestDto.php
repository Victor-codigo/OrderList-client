<?php

declare(strict_types=1);

namespace App\Controller\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    public function __construct(
        public readonly string|null $tokenSession,
        public readonly array|null $groupData,
        public readonly Request $request
    ) {
    }
}
