<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    public function __construct(
        public readonly string|null $tokenSession,
        public readonly string|null $groupName,
        public readonly GroupDataResponse|null $groupData,
        public readonly ShopDataResponse|null $shopData,
        public readonly Request $request
    ) {
    }
}
