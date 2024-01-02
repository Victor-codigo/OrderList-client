<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductsEndPoint extends EndpointBase
{
    public const POST_PRODUCT_CREATE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/products';
    public const POST_PRODUCT_MODIFY = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/products';
    public const DELETE_PRODUCT_DELETE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/products';
    public const GET_PRODUCT_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/products';

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
     *
     * @throws UnsupportedOptionException
     */
    public function productCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array
    {
        $response = $this->requestProductCreate($groupId, $name, $description, $image, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestProductCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::POST_PRODUCT_CREATE,
            HTTP_CLIENT_CONFIGURATION::form([
                    'group_id' => $groupId,
                    'name' => $name,
                    'description' => $description,
                ],
                [
                    'image' => $image,
                ],
                $tokenSession
            )
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function productModify(string $groupId, string $productId, string|null $shopId, string|null $name, string|null $description, float|null $price, UploadedFile|null $image, bool $imageRemove, string $tokenSession): array
    {
        $response = $this->requestProductModify($groupId, $productId, $shopId, $name, $description, $price, $image, $imageRemove, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    private function requestProductModify(string $groupId, string $productId, string|null $shopId, string|null $name, string|null $description, float|null $price, UploadedFile|null $image, bool $imageRemove, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::POST_PRODUCT_MODIFY,
            HTTP_CLIENT_CONFIGURATION::form($this->createFormParameters([
                    'group_id' => $groupId,
                    'product_id' => $productId,
                    'shop_id' => $shopId,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'image_remove' => $imageRemove,
                    '_method' => 'PUT',
                ]),
                $this->createFormParameters([
                     'image' => $image,
                 ]),
                $tokenSession
            )
        );
    }

    /**
     * @param string[] $shopsId
     * @param string[] $productsId
     *
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function productRemove(string $groupId, array $productsId, array|null $shopsId, string $tokenSession): array
    {
        $response = $this->requestProductRemove($groupId, $productsId, $shopsId, $tokenSession);

        return $this->apiResponseManage($response);
    }

    /**
     * @param string[] $shopsId
     * @param string[] $productsId
     *
     * @throws UnsupportedOptionException
     */
    private function requestProductRemove(string $groupId, array $productsId, array|null $shopsId, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::DELETE_PRODUCT_DELETE,
            HTTP_CLIENT_CONFIGURATION::json($this->createFormParameters([
                    'group_id' => $groupId,
                    'products_id' => $productsId,
                    'shops_id' => $shopsId,
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
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function productGetData(
        string $groupId,
        array|null $productsId,
        array|null $shopsId,
        string|null $productName,
        string|null $productNameFilterType,
        string|null $productNameFilterValue,
        string|null $shopNameFilterFilter,
        string|null $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        $response = $this->requestProductGetData(
            $groupId,
            $productsId,
            $shopsId,
            $productName,
            $productNameFilterType,
            $productNameFilterValue,
            $shopNameFilterFilter,
            $shopNameFilterValue,
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
                'errors' => ['product_not_found' => 'Product not found'],
            ]
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestProductGetData(
        string $groupId,
        array|null $productsId,
        array|null $shopsId,
        string|null $productName,
        string|null $productNameFilterType,
        string|null $productNameFilterValue,
        string|null $shopNameFilterFilter,
        string|null $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): HttpClientResponseInterface {
        $parameters = [
            'group_id' => $groupId,
            'page' => $page,
            'page_items' => $pageItems,
            'products_id' => null !== $productsId ? implode(',', $productsId) : null,
            'shops_id' => null !== $shopsId ? implode(',', $shopsId) : null,
            'product_name' => $productName,
            'order_asc' => $orderAsc,
            'product_name_filter_type' => $productNameFilterType,
            'product_name_filter_value' => $productNameFilterValue,
            'shop_name_filter_type' => $shopNameFilterFilter,
            'shop_name_filter_value' => $shopNameFilterValue,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_PRODUCT_DATA."?{$this->createQueryParameters($parameters)}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
