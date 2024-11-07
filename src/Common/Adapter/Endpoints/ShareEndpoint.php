<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class ShareEndpoint extends EndpointBase
{
    private const string GET_SHARE_LIST_ORDERS = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/share/list-orders';

    private static ?self $instance = null;

    private function __construct(
        private HttpClientInterface $httpClient,
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
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function getListOrdersDataById(string $sharedListOrdersId, int $page, int $pageItems, ?string $filterText, ?string $filterValue): array
    {
        $response = $this->requestListOrdersDataById($sharedListOrdersId, $page, $pageItems, $filterText, $filterValue);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [],
                'errors' => ['lists_orders_not_found' => 'List orders not found'],
            ],
            null,
            fn (array $responseDataNoContent) => [
                'data' => [],
                'errors' => [],
            ]);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestListOrdersDataById(string $sharedListOrdersId, int $page, int $pageItems, ?string $filterText, ?string $filterValue): HttpClientResponseInterface
    {
        $queryParameters = [
            'shared_list_orders_id' => $sharedListOrdersId,
            'page' => $page,
            'page_items' => $pageItems,
            'filter_text' => $filterText,
            'filter_value' => $filterValue,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_SHARE_LIST_ORDERS."?{$this->createQueryParameters($queryParameters)}",
            HTTP_CLIENT_CONFIGURATION::json([])
        );
    }
}
