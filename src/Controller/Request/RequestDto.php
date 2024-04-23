<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\OrderDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Controller\Request\Response\UserDataResponse;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    /**
     * @param \Closure<UserDataResponse> $userSessionData
     */
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
        private readonly \Closure $userSessionData,
        public readonly ?GroupDataResponse $groupData,
        private readonly \Closure $shopData,
        private readonly \Closure $productData,
        private readonly \Closure $listOrdersData,
        public readonly \Closure $orderData,
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

    public function getUserSessionData(): ?UserDataResponse
    {
        static $userData;

        if (null === $userData) {
            $userData = ($this->userSessionData)($this->tokenSession);
        }

        return $userData;
    }

    public function getShopData(): ?ShopDataResponse
    {
        static $shopData;

        if (null === $shopData) {
            $shopData = ($this->shopData)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $shopData;
    }

    public function getProductData(): ?ProductDataResponse
    {
        static $productData;

        if (null === $productData) {
            $productData = ($this->productData)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $productData;
    }

    public function getListOrdersData(): ?ListOrdersDataResponse
    {
        static $listOrdersData;

        if (null === $listOrdersData) {
            $listOrdersData = ($this->listOrdersData)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $listOrdersData;
    }

    public function getOrdersData(): ?OrderDataResponse
    {
        static $ordersData;

        if (null === $ordersData) {
            $ordersData = ($this->orderData)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $ordersData;
    }
}
