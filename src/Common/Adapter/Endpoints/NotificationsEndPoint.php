<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class NotificationsEndPoint extends EndpointBase
{
    public const DELETE_NOTIFICATION_DELETE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/notification';
    public const GET_NOTIFICATION_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/notification';
    public const PATCH_NOTIFICATION_MARK_AS_VIEWED = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/notification/mark-as-viewed';

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
     * @throws RequestUnauthorizedException
     */
    public function notificationRemove(array $notificationsId, string $tokenSession): array
    {
        $response = $this->requestNotificationsRemove($notificationsId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestNotificationsRemove(array $notificationsId, string $tokenSession): HttpClientResponseInterface
    {
        $parameters = [
            'notifications_id' => implode(',', $notificationsId),
        ];

        return $this->httpClient->request(
            'DELETE',
            self::DELETE_NOTIFICATION_DELETE."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession));
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
    public function notificationGetData(int $page, int $pageItems, string $lang, string $tokenSession): array
    {
        $response = $this->requestNotificationGetData($page, $pageItems, $lang, $tokenSession);

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
    private function requestNotificationGetData(int $page, int $pageItems, string $lang, string $tokenSession): HttpClientResponseInterface
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

    /**
     * @param string[] $notificationsId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function notificationMarkAsViewed(array $notificationsId, string $tokenSession): array
    {
        $response = $this->requestNotificationsMarkAsViewed($notificationsId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestNotificationsMarkAsViewed(array $notificationsId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'PATCH',
            self::PATCH_NOTIFICATION_MARK_AS_VIEWED,
            HTTP_CLIENT_CONFIGURATION::json($this->createFormParameters([
                'notifications_id' => $notificationsId,
            ]),
                $tokenSession
            )
        );
    }
}
