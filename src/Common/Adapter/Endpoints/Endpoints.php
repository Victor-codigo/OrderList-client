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

    private function decodeUrlName(string|null $name): string|null
    {
        if (null === $name) {
            return null;
        }

        return str_replace('-', ' ', $name);
    }

    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetOrders($groupId, $listOrdersId, $page, $pageItems, $tokenSession);
    }

    public function listOrdersGetData(string $groupId, string $listOrderName, string $tokenSession): array
    {
        $listOrderNameDecoded = $this->decodeUrlName($listOrderName);

        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetData($groupId, $listOrderNameDecoded, $tokenSession);
    }

    /**
     * @return array<string, array> "data" and "errors" as index
     */
    public function listOrdersDeleteOrders(string $groupId, string $listOrdersId, array $ordersId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersDeleteOrders($groupId, $listOrdersId, $ordersId, $tokenSession);
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

    public function productCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->productCreate($groupId, $name, $description, $image, $tokenSession);
    }
}