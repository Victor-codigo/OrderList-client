<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\HttpClient\Exception\UnsupportedOptionException;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class ListOrdersEndpoints extends EndpointBase
{
    private const GET_LIST_ORDERS_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders/order';
    private const GET_LIST_ORDERS_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders';
    private const REMOVE_LIST_ORDERS_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders/order';

    private static self|null $instance = null;

    private function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    public static function getInstance(HttpClientInterface $httpClient): self
    {
        if (null === self::$instance) {
            return new self($httpClient);
        }

        return self::$instance;
    }

    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array
    {
        try {
            $response = $this->requestListOrdersOrders($groupId, $listOrdersId, $page, $pageItems, $tokenSession);
            $responseData = $response->toArray();

            return $responseData['data'];
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = $response->toArray(false);

            return [
                'page' => $page,
                'pages_total' => 0,
                'orders' => [],
            ];
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            self::GET_LIST_ORDERS_ORDERS
                ."?group_id={$groupId}"
                ."&list_order_id={$listOrdersId}"
                ."&page={$page}"
                ."&page_items={$pageItems}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    public function listOrdersGetData(string $groupId, string $listOrderName, string $tokenSession): array
    {
        try {
            $response = $this->requestListOrdersData($groupId, $listOrderName, $tokenSession);
            $responseData = $response->toArray();
            $listOrdersData = $responseData['data'][0];
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $listOrdersData = [];
        } finally {
            return $listOrdersData;
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersData(string $groupId, string $listOrdersName, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            self::GET_LIST_ORDERS_DATA
                ."?group_id={$groupId}"
                ."&list_orders_name_starts_with={$listOrdersName}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @return array<string, array> "data" and "errors" as index
     */
    public function listOrdersDeleteOrders(string $groupId, string $listOrdersId, array $ordersId, string $tokenSession): array
    {
        try {
            $response = $this->requestRemoveOrder($groupId, $listOrdersId, $ordersId, $tokenSession);
            $responseData = $response->toArray();
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = $e->getResponse()->toArray(false);
        } finally {
            $ordersDeletedId = [
                'data' => $responseData['data'],
                'errors' => $responseData['errors'],
            ];

            return $ordersDeletedId;
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestRemoveOrder(string $groupId, string $listOrderId, array $ordersId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::REMOVE_LIST_ORDERS_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json([
                'list_orders_id' => $listOrderId,
                'group_id' => $groupId,
                'orders_id' => $ordersId,
            ], $tokenSession)
        );
    }
}
