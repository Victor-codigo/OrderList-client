<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    public function __construct(
        public readonly string|null $tokenSession,
        public readonly string|null $locale,
        public readonly string|null $groupNameUrlEncoded,
        public readonly string|null $shopNameUrlEncoded,
        public readonly string|null $productNameUrlEncoded,
        public readonly int|null $page,
        public readonly int|null $pageItems,
        public readonly GroupDataResponse|null $groupData,
        public readonly ShopDataResponse|null $shopData,
        public readonly ProductDataResponse|null $productData,
        public readonly ListOrdersDataResponse|null $listOrdersData,
        public readonly Request $request,
        public readonly RequestRefererDto|null $requestReferer
    ) {
    }
}
