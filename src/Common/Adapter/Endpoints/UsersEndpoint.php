<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class UsersEndpoint extends EndpointBase
{
    private const POST_LOGIN_ENDPOINT = '/api/v1/users/login';
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

    private function getCookieValue(string $cookie): ?string
    {
        $cookieParams = explode(';', $cookie);

        if (count($cookieParams) < 1) {
            return null;
        }

        $cookieKeyValue = explode('=', $cookieParams[0]);

        if (2 !== count($cookieKeyValue)) {
            return null;
        }

        return $cookieKeyValue[1];
    }

    /**
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function login(string $userName, string $password): ?string
    {
        $response = $this->requestLogin($userName, $password);
        $tokenSession = $this->apiResponseManage($response,
            fn (array $responseDataError) => null,
            fn (array $responseDataOk) => null,
            function (array $responseDataNoContent) use ($response): ?string {
                $headers = $response->getHeaders();

                return $this->getCookieValue($headers['set-cookie'][0]);
            });

        if (null === $tokenSession) {
            return null;
        }

        return (string) $tokenSession;
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestLogin(string $userName, string $password): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            HTTP_CLIENT_CONFIGURATION::API_DOMAIN.self::POST_LOGIN_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'username' => $userName,
                'password' => $password,
            ])
        );
    }
}
