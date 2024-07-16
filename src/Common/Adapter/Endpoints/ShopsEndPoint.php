<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ShopsEndPoint extends EndpointBase
{
    public const POST_SHOP_CREATE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';
    public const PUT_SHOP_MODIFY = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';
    public const GET_SHOP_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';
    public const DELETE_SHOP_REMOVE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';

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
    public function shopCreate(string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        $response = $this->requestShopCreate($groupId, $name, $address, $description, $image, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopCreate(string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, string $tokenSession): HttpClientResponseInterface
    {
        $formData = [
            'group_id' => $groupId,
            'name' => $name,
        ];

        if (null !== $address) {
            $formData['address'] = $address;
        }

        if (null !== $description) {
            $formData['description'] = $description;
        }

        return $this->httpClient->request(
            'POST',
            self::POST_SHOP_CREATE.'?'.HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::form(
                $formData,
                [
                    'image' => $image,
                ],
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function shopModify(string $shopId, string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        $response = $this->requestShopModify($shopId, $groupId, $name, $address, $description, $image, $imageRemove, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopModify(string $shopId, string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): HttpClientResponseInterface
    {
        $formData = [
            'shop_id' => $shopId,
            'group_id' => $groupId,
            'name' => $name,
            'image_remove' => $imageRemove,
            '_method' => 'PUT',
        ];

        if (null !== $address) {
            $formData['address'] = $address;
        }

        if (null !== $description) {
            $formData['description'] = $description;
        }

        return $this->httpClient->request(
            'POST',
            self::PUT_SHOP_MODIFY,
            HTTP_CLIENT_CONFIGURATION::form(
                $formData,
                [
                    'image' => $image,
                ],
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function shopsGetData(
        string $groupId,
        ?array $shopsId,
        ?array $productsId,
        ?string $shopName,
        ?string $shopNameFilterType,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        $response = $this->requestShopsGetData(
            $groupId,
            $shopsId,
            $productsId,
            $shopName,
            $shopNameFilterType,
            $shopNameFilterValue,
            $page,
            $pageItems,
            $orderAsc,
            $tokenSession
        );

        return $this->apiResponseManage($response,
            fn (array $responseDataError): array => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'shops' => [],
                ],
                'errors' => $responseDataError,
            ], null,
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'shops' => [],
                ],
                'errors' => ['shop_not_found' => 'Shop not found'],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopsGetData(
        string $groupId,
        ?array $shopsId,
        ?array $productsId,
        ?string $shopName,
        ?string $shopNameFilterType,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): HttpClientResponseInterface {
        $parameters = [
            'group_id' => $groupId,
            'shops_id' => null !== $shopsId ? implode(',', $shopsId) : null,
            'products_id' => null !== $productsId ? implode(',', $productsId) : null,
            'page' => $page,
            'page_items' => $pageItems,
            'shop_name' => $shopName,
            'order_asc' => $orderAsc,
            'shop_name_filter_type' => $shopNameFilterType,
            'shop_name_filter_value' => $shopNameFilterValue,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_SHOP_DATA."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function shopRemove(string $groupId, array $shopsId, string $tokenSession): array
    {
        $response = $this->requestShopRemove($groupId, $shopsId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopRemove(string $groupId, array $shopsId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::DELETE_SHOP_REMOVE,
            HTTP_CLIENT_CONFIGURATION::json([
                'group_id' => $groupId,
                'shops_id' => $shopsId,
            ],
                $tokenSession
            )
        );
    }
}
