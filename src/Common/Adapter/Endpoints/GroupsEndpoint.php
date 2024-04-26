<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GroupsEndpoint extends EndpointBase
{
    public const GET_GROUP_ID_BY_NAME = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/groups/data/name/{group_name}';
    public const GET_USER_GROUPS_GET_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/groups/user-groups';
    private const POST_GROUP_CREATE = Endpoints::API_DOMAIN.'/api/v1/groups';
    private const PUT_GROUP_MODIFY = Endpoints::API_DOMAIN.'/api/v1/groups/modify';
    private const DELETE_GROUP_DELETE = Endpoints::API_DOMAIN.'/api/v1/groups';

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

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function userGroupsGetData(
        ?string $filterSection,
        ?string $filterText,
        ?string $filterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        $response = $this->requestUserGroupsGetData(
            $filterSection,
            $filterText,
            $filterValue,
            $page,
            $pageItems,
            $orderAsc,
            $tokenSession
        );

        return $this->apiResponseManage($response, null, null,
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 0,
                    'products' => [],
                ],
                'errors' => ['group_not_found' => 'Group not found'],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestUserGroupsGetData(
        ?string $filterSection,
        ?string $filterText,
        ?string $filterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): HttpClientResponseInterface {
        $parameters = [
            'page' => $page,
            'page_items' => $pageItems,
            'filter_section' => $filterSection,
            'filter_text' => $filterText,
            'filter_value' => $filterValue,
            'order_asc' => $orderAsc,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_USER_GROUPS_GET_DATA."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupCreate(string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        $response = $this->requestGroupCreate($name, $description, $image, $tokenSession);

        return $this->apiResponseManage($response);
    }

    private function requestGroupCreate(string $name, ?string $description, ?UploadedFile $image, string $tokenSession): HttpClientResponseInterface
    {
        $files = [];
        if (null !== $image) {
            $files = [
                'image' => $image,
            ];
        }

        return $this->httpClient->request(
            'POST',
            self::POST_GROUP_CREATE,
            HTTP_CLIENT_CONFIGURATION::form([
                'name' => $name,
                'description' => $description,
                'type' => 'TYPE_GROUP',
            ],
                $files,
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
     * @throws UnsupportedOptionException
     */
    public function groupModify(string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        $response = $this->requestGroupModify($groupId, $name, $description, $image, $imageRemove, $tokenSession);

        return $this->apiResponseManage($response);
    }

    private function requestGroupModify(string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): HttpClientResponseInterface
    {
        $file = [];
        if (null !== $image) {
            $file = [
                'image' => $image,
            ];
        }

        return $this->httpClient->request(
            'POST',
            self::PUT_GROUP_MODIFY,
            HTTP_CLIENT_CONFIGURATION::form([
                'group_id' => $groupId,
                'name' => $name,
                'description' => $description,
                'image_remove' => $imageRemove,
                '_method' => 'PUT',
            ],
                $file,
                $tokenSession
            )
        );
    }

    /**
     * @param string[] $groupsId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupRemove(array $groupsId, string $tokenSession): array
    {
        $response = $this->requestGroupRemove($groupsId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    private function requestGroupRemove(array $groupId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::DELETE_GROUP_DELETE,
            HTTP_CLIENT_CONFIGURATION::json([
                'groups_id' => $groupId,
            ],
                $tokenSession)
        );
    }
}
