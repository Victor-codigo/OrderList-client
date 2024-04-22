<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\OrderDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    public function __construct(
        private readonly ?string $tokenSession,

        public readonly ?string $locale,
        public readonly ?string $sectionActiveId,
        public readonly ?string $userNameUrlEncoded,
        public readonly ?string $groupNameUrlEncoded,
        public readonly ?string $listOrdersUrlEncoded,
        public readonly ?string $shopNameUrlEncoded,
        public readonly ?string $productNameUrlEncoded,
        public readonly ?int $page,
        public readonly ?int $pageItems,
        public readonly ?GroupDataResponse $groupData,
        public readonly ?ShopDataResponse $shopData,
        public readonly ?ProductDataResponse $productData,
        public readonly ?ListOrdersDataResponse $listOrdersData,
        public readonly ?OrderDataResponse $orderData,
        public readonly Request $request,
        public readonly ?RequestRefererDto $requestReferer
    ) {
    }

    /**
     * @throws RequestUnauthorizedException
     */
    public function getTokenSessionOrFail(): string
    {
        if (null === $this->tokenSession) {
            throw RequestUnauthorizedException::fromMessage('Token session missing');
        }

        return $this->tokenSession;
    }
}
