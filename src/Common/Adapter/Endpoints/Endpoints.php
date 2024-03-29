<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Endpoints implements EndpointsInterface
{
    public const API_VERSION = 1;
    public const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public function decodeUrlName(?string $name): ?string
    {
        if (null === $name) {
            return null;
        }

        return str_replace('-', ' ', $name);
    }

    public function encodeUrl(string $url): string
    {
        return str_replace(' ', '-', $url);
    }

    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetOrders($groupId, $listOrdersId, $page, $pageItems, $tokenSession);
    }

    public function listOrdersGetData(
        string $groupId,
        ?array $listOrdersId,
        bool $orderAsc,
        ?string $filterValue,
        ?string $filterSection,
        ?string $filterText,
        int $page,
        int $pageItems,
        string $tokenSession
    ): array {
        $listOrderNameDecoded = $this->decodeUrlName($filterValue);

        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetData(
            $groupId,
            $listOrdersId,
            $orderAsc,
            $listOrderNameDecoded,
            $filterSection,
            $filterText,
            $page,
            $pageItems,
            $tokenSession
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreate(string $groupId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersCreate($groupId, $name, $description, $dateToBuy, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersModify(string $groupId, string $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersModify($groupId, $listOrdersId, $name, $description, $dateToBuy, $tokenSession);
    }

    /**
     * @return array<string, array> "data" and "errors" as index
     */
    public function listOrdersRemove(string $groupId, array $listsOrdersId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersRemove($groupId, $listsOrdersId, $tokenSession);
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersRemoveOrders(string $groupId, array $listsOrdersId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersRemoveOrders($groupId, $listsOrdersId, $tokenSession);
    }

    public function groupGetDataByName(string $groupName, string $tokenSession): array
    {
        $groupNameDecoded = $this->decodeUrlName($groupName);

        return GroupsEndpoints::getInstance($this->httpClient)->groupGetDataByName($groupNameDecoded, $tokenSession);
    }

    /**
     * @return array<string, mixed> index: page -> int,
     *                              pages_total -> int,
     *                              orders -> array of orders
     */
    public function ordersGroupGetData(string $groupId, int $page, int $pageItems, string $tokenSession): array
    {
        return OrdersEndpoints::getInstance($this->httpClient)->ordersGroupGetData($groupId, $page, $pageItems, $tokenSession);
    }

    public function ordersDelete(string $groupId, array $ordersId, string $tokenSession): array
    {
        return OrdersEndpoints::getInstance($this->httpClient)->ordersDelete($groupId, $ordersId, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function productCreate(string $groupId, string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->productCreate($groupId, $name, $description, $image, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function productModify(
        string $groupId,
        string $productId,
        ?string $shopId,
        ?string $name,
        ?string $description,
        ?float $price,
        ?UploadedFile $image,
        bool $imageRemove,
        string $tokenSession
    ): array {
        return ProductsEndPoint::getInstance($this->httpClient)->productModify(
            $groupId,
            $productId,
            $shopId,
            $name,
            $description,
            $image,
            $imageRemove,
            $tokenSession
        );
    }

    /**
     * @param string[] $shopsId
     * @param string[] $productsId
     *
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function productRemove(string $groupId, array $productsId, ?array $shopsId, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->productRemove($groupId, $productsId, $shopsId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function productGetData(
        string $groupId,
        ?array $productsId,
        ?array $shopsId,
        ?string $productName,
        ?string $productNameFilterType,
        ?string $productNameFilterValue,
        ?string $shopNameFilterFilter,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        return ProductsEndPoint::getInstance($this->httpClient)->productGetData(
            $groupId,
            $productsId,
            $shopsId,
            $productName,
            $productNameFilterType,
            $productNameFilterValue,
            $shopNameFilterFilter,
            $shopNameFilterValue,
            $page,
            $pageItems,
            $orderAsc,
            $tokenSession
        );
    }

    /**
     * @param string[] $productsOrShopsId
     * @param float[]  $prices
     *
     * @throws UnsupportedOptionException
     */
    public function setProductShopPrice(string $groupId, ?string $productId, ?string $shopId, array $productsOrShopsId, array $prices, array $unitsMeasure, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->setProductShopPrice($groupId, $productId, $shopId, $productsOrShopsId, $prices, $unitsMeasure, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function getProductShopPrice(string $groupId, array $productsId, array $shopsId, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->getProductShopPrice($groupId, $productsId, $shopsId, $tokenSession);
    }

    public function shopCreate(string $groupId, string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopCreate($groupId, $name, $description, $image, $tokenSession);
    }

    public function shopModify(string $shopId, string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopModify($shopId, $groupId, $name, $description, $image, $imageRemove, $tokenSession);
    }

    public function shopsGetData(
        string $groupId,
        ?array $shopsId,
        ?array $productsId,
        ?string $shopName,
        ?string $shopNameFilterType,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        return ShopsEndPoint::getInstance($this->httpClient)->shopsGetData(
            $groupId,
            $shopsId,
            $productsId,
            $shopName,
            $shopNameFilterType,
            $shopNameFilterValue,
            $page,
            $pageItems,
            $orderAsc,
            $tokenSession
        );
    }

    public function shopRemove(string $groupId, ?array $shopsId, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopRemove($groupId, $shopsId, $tokenSession);
    }
}
