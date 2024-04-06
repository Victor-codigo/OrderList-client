<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\UnsupportedOptionException;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class ListOrdersEndpoints extends EndpointBase
{
    public const CREATE_LIST_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders';
    public const MODIFY_LIST_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders';
    public const REMOVE_LIST_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders';
    public const REMOVE_LIST_ORDERS_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders/orders';
    public const GET_LIST_ORDERS_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders';
    public const GET_LIST_ORDERS_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders/order';
    public const GET_LIST_ORDERS_PRICE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/list-orders/price';

    private static ?self $instance = null;

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
     *    page: int
     *    pages_total: int,
     *    orders: array<int, array>
     * }>
     */
    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array
    {
        $response = $this->requestListOrdersOrders($groupId, $listOrdersId, $page, $pageItems, $tokenSession);

        return $this->apiResponseManage($response, function (array $responseDataError) use ($page) {
            return [
                'page' => $page,
                'pages_total' => 0,
                'orders' => [],
            ];
        });
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

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
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
        $response = $this->requestListOrdersData(
            $groupId,
            $listOrdersId,
            $orderAsc,
            $filterValue,
            $filterSection,
            $filterText,
            $page,
            $pageItems,
            $tokenSession
        );

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 0,
                    'list_orders' => [],
                ],
                'errors' => ['lists_orders_not_found' => 'List orders not found'],
            ],
            null,
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 0,
                    'list_orders' => [],
                ],
                'errors' => [],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersData(
        string $groupId,
        ?array $listOrdersId,
        bool $orderAsc,
        ?string $filterValue,
        ?string $filterSection,
        ?string $filterText,
        int $page,
        int $pageItems,
        string $tokenSession
    ): HttpClientResponseInterface {
        $parameters = [
            'group_id' => $groupId,
            'list_orders_id' => empty($listOrdersId) ? null : implode(',', $listOrdersId),
            'order_asc' => $orderAsc,
            'filter_value' => $filterValue,
            'filter_section' => $filterSection,
            'filter_text' => $filterText,
            'page' => $page,
            'page_items' => $pageItems,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_LIST_ORDERS_DATA."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersGetPrice(?array $listOrdersId, string $groupId, string $tokenSession): array
    {
        $response = $this->requestListOrdersGetPrice(
            $listOrdersId,
            $groupId,
            $tokenSession
        );

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [
                    'total' => 0,
                    'bought' => 0,
                ],
                'errors' => $responseDataError,
            ],
            null,
            fn (array $responseDataNoContent) => [
                'data' => [
                    'total' => 0,
                    'bought' => 0,
                ],
                'errors' => [],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersGetPrice(?array $listOrdersId, string $groupId, string $tokenSession): HttpClientResponseInterface
    {
        $parameters = [
            'list_orders_id' => empty($listOrdersId) ? null : implode(',', $listOrdersId),
            'group_id' => $groupId,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_LIST_ORDERS_PRICE."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersRemove(string $groupId, array $listsOrdersId, string $tokenSession): array
    {
        $response = $this->requestRemoveListOrders($groupId, $listsOrdersId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @throws UnsupportedOptionException
     */
    private function requestRemoveListOrders(string $groupId, array $listsOrdersId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::REMOVE_LIST_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json([
                'group_id' => $groupId,
                'lists_orders_id' => $listsOrdersId,
            ],
                $tokenSession
            ));
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
        $response = $this->requestRemoveListOrdersOrders($groupId, $listsOrdersId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @throws UnsupportedOptionException
     */
    private function requestRemoveListOrdersOrders(string $groupId, array $listsOrdersId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::REMOVE_LIST_ORDERS_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json([
                'lists_orders_id' => $listsOrdersId,
                'group_id' => $groupId,
            ],
                $tokenSession
            ));
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws RequestUnauthorizedException
     * @throws RequestException
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreate(string $groupId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        $response = $this->requestListOrdersCreate($groupId, $name, $description, $dateToBuy, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersCreate(string $groupId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::CREATE_LIST_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json($this->createFormParameters([
                'group_id' => $groupId,
                'name' => $name,
                'description' => $description,
                'date_to_buy' => $dateToBuy?->format('Y-m-d H:i:s'),
            ]),
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws RequestUnauthorizedException
     * @throws RequestException
     * @throws UnsupportedOptionException
     */
    public function listOrdersModify(string $groupId, $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        $response = $this->requestListOrdersModify($groupId, $listOrdersId, $name, $description, $dateToBuy, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersModify(string $groupId, string $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'PUT',
            self::CREATE_LIST_ORDERS,
            HTTP_CLIENT_CONFIGURATION::json($this->createFormParameters([
                'group_id' => $groupId,
                'list_orders_id' => $listOrdersId,
                'name' => $name,
                'description' => $description,
                'date_to_buy' => $dateToBuy?->format('Y-m-d H:i:s'),
            ]),
                $tokenSession
            )
        );
    }
}
