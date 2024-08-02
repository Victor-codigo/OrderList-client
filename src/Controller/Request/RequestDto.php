<?php

declare(strict_types=1);

namespace App\Controller\Request;

use App\Controller\Request\Response\GroupDataResponse;
use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Controller\Request\Response\NotificationDataResponse;
use App\Controller\Request\Response\OrderDataResponse;
use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Controller\Request\Response\UserDataResponse;
use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\Request;

class RequestDto
{
    /**
     * @param \Closure<UserDataResponse> $userSessionDataCallback
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
        private readonly \Closure $userSessionDataCallback,
        private readonly \Closure $notificationsDataCallback,
        public readonly ?GroupDataResponse $groupData,
        private readonly \Closure $shopDataCallback,
        private readonly \Closure $productDataCallback,
        private readonly \Closure $listOrdersDataCallback,
        private readonly \Closure $orderDataCallback,
        public readonly Request $request,
        public readonly ?RequestRefererDto $requestReferer
    ) {
    }

    /**
     * @throws RequestUnauthorizedException
     * @throws JwtTokenGetPayLoadException
     */
    public function getTokenSessionOrFail(): string
    {
        if (null === $this->tokenSession || JwtToken::hasExpired($this->tokenSession)) {
            throw RequestUnauthorizedException::fromMessage('Token session missing or expired');
        }

        return $this->tokenSession;
    }

    public function getUserSessionData(): ?UserDataResponse
    {
        static $userData;

        if (null === $userData) {
            $userData = ($this->userSessionDataCallback)($this->tokenSession);
        }

        return $userData;
    }

    /**
     * @return NotificationDataResponse[]
     */
    public function getNotificationsData(): array
    {
        static $notificationsData;

        if (null === $notificationsData) {
            $notificationsData = ($this->notificationsDataCallback)($this->locale, $this->tokenSession);
        }

        return $notificationsData;
    }

    public function getShopData(): ?ShopDataResponse
    {
        static $shopData;

        if (null === $shopData) {
            $shopData = ($this->shopDataCallback)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $shopData;
    }

    public function getProductData(): ?ProductDataResponse
    {
        static $productData;

        if (null === $productData) {
            $productData = ($this->productDataCallback)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $productData;
    }

    public function getListOrdersData(): ?ListOrdersDataResponse
    {
        static $listOrdersData;

        if (null === $listOrdersData) {
            $listOrdersData = ($this->listOrdersDataCallback)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $listOrdersData;
    }

    public function getOrdersData(): ?OrderDataResponse
    {
        static $ordersData;

        if (null === $ordersData) {
            $ordersData = ($this->orderDataCallback)($this->request->attributes, $this->groupData?->id, $this->tokenSession);
        }

        return $ordersData;
    }
}
