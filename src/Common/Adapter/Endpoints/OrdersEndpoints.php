<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class OrdersEndpoints extends EndpointBase
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

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function ordersDelete(string $groupId, array $ordersId, string $tokenSession): array
    {
        $response = $this->requestDeleteOrder($groupId, $ordersId, $tokenSession);

        return $this->apiResponseManage($response);
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
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    orders: array<int, array>
     * }>
     */
    public function ordersGroupGetData(string $groupId, int $page, int $pageItems, string $tokenSession): array
    {
        $response = $this->requestGetOrdersGroup($groupId, $page, $pageItems, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'page' => $page,
                'pages_total' => 0,
                'orders' => [],
            ],
            fn (array $responseDataOk) => $responseDataOk['data']
        );
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
