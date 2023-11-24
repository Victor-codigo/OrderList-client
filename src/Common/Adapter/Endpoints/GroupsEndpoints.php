<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;

class GroupsEndpoints extends EndpointBase
{
    private const GET_GROUP_ID_BY_NAME = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/groups/data/name/{group_name}';

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
    public function groupGetDataByName(string $groupName, string $tokenSession): array
    {
        $response = $this->requestGroupData($groupName, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestGroupData(string $groupName, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'GET',
            str_replace('{group_name}', $groupName, self::GET_GROUP_ID_BY_NAME),
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
