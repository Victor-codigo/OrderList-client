<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class OrdersEndpoints
{
    private const DELETE_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/orders';
    private const GET_ORDERS_GROUP = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/orders/group/{group_id}';

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

    public function ordersDelete(string $groupId, array $ordersId, string $tokenSession): array
    {
        try {
            $response = $this->requestDeleteOrder($groupId, $ordersId, $tokenSession);
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
    private function requestDeleteOrder(string $groupId, array $ordersId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::DELETE_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json([
                'group_id' => $groupId,
                'orders_id' => $ordersId,
            ], $tokenSession)
        );
    }

    /**
     * @return array<string, mixed> index: page -> int,
     *                              pages_total -> int,
     *                              orders -> array of orders
     */
    public function ordersGroupGetData(string $groupId, int $page, int $pageItems, string $tokenSession): array
    {
        try {
            $response = $this->requestGetOrdersGroup($groupId, $page, $pageItems, $tokenSession);
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
    private function requestGetOrdersGroup(string $groupId, int $page, int $pageItems, string $tokenSession): HttpClientResponseInterface
    {
        $urlEndpoint = str_replace('{group_id}', $groupId, self::GET_ORDERS_GROUP);

        return $this->httpClient->request(
            'GET',
            $urlEndpoint
                ."?page={$page}"
                ."&page_items={$pageItems}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
