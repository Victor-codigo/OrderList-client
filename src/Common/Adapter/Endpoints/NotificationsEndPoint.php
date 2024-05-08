<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class NotificationsEndPoint extends EndpointBase
{
    // public const DELETE_PRODUCT_DELETE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/notification';
    public const GET_NOTIFICATION_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/notification';

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
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    // public function productRemove(string $groupId, array $productsId, array|null $shopsId, string $tokenSession): array
    // {
    //     $response = $this->requestProductRemove($groupId, $productsId, $shopsId, $tokenSession);

    //     return $this->apiResponseManage($response);
    // }

    /**
     * @throws UnsupportedOptionException
     */
    // private function requestProductRemove(string $groupId, array $productsId, array|null $shopsId, string $tokenSession): HttpClientResponseInterface
    // {
    //     return $this->httpClient->request(
    //         'DELETE',
    //         self::DELETE_PRODUCT_DELETE,
    //         HTTP_CLIENT_CONFIGURATION::json($this->createFormParameters([
    //                 'group_id' => $groupId,
    //                 'products_id' => $productsId,
    //                 'shops_id' => $shopsId,
    //             ]),
    //             $tokenSession
    //         )
    //     );
    // }

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
    public function productGetData(int $page, int $pageItems, string $lang, string $tokenSession): array
    {
        $response = $this->requestProductGetData($page, $pageItems, $lang, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 0,
                    'notifications' => [],
                ],
                'errors' => $responseDataError,
            ],
            fn (array $responseDataOk) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'notifications' => $responseDataOk['data'],
                ],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 0,
                    'notifications' => [],
                ],
                'errors' => ['notification_not_found' => 'Notification not found'],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestProductGetData(int $page, int $pageItems, string $lang, string $tokenSession): HttpClientResponseInterface
    {
        $parameters = [
            'page' => $page,
            'page_items' => $pageItems,
            'lang' => $lang,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_NOTIFICATION_DATA."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
