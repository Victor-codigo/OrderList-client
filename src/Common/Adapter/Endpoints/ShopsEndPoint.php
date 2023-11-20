<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\DecodingException;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ShopsEndPoint extends EndpointBase
{
    private const POST_SHOP_CREATE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';
    private const PUT_SHOP_MODIFY = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';
    private const GET_SHOP_DATA = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/shops';

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

    public function shopCreate(string $groupId, string $name, string|null $description, UploadedFile|null $image, string $tokenSession): array
    {
        try {
            $response = $this->requestShopCreate($groupId, $name, $description, $image, $tokenSession);
            $responseData = $response->toArray();
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = $e->getResponse()->toArray(false);
        } finally {
            return [
                'data' => $responseData['data'],
                'errors' => $responseData['errors'],
            ];
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopCreate(string $groupId, string $name, string|null $description, UploadedFile|null $image, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::POST_SHOP_CREATE,
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

    public function shopModify(string $shopId, string $groupId, string $name, string $description, UploadedFile|null $image, bool $imageRemove, string $tokenSession): array
    {
        try {
            $response = $this->requestShopModify($shopId, $groupId, $name, $description, $image, $imageRemove, $tokenSession);
            $responseData = $response->toArray();
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = $e->getResponse()->toArray(false);
        } finally {
            return [
                'data' => $responseData['data'],
                'errors' => $responseData['errors'],
            ];
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopModify(string $shopId, string $groupId, string $name, string $description, UploadedFile|null $image, bool $imageRemove, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::PUT_SHOP_MODIFY,
            HTTP_CLIENT_CONFIGURATION::form([
                    'shop_id' => $shopId,
                    'group_id' => $groupId,
                    'name' => $name,
                    'description' => $description,
                    'image_remove' => $imageRemove,
                    '_method' => 'PUT',
                ],
                [
                    'image' => $image,
                ],
                $tokenSession
            )
        );
    }

    public function shopsGetData(string $groupId, string|null $shopsId, string|null $productsId, string|null $shopName, string|null $shopNameStartsWith, string $tokenSession): array
    {
        try {
            $response = $this->requestShopsGetData(
                $groupId,
                $shopsId,
                $productsId,
                $shopName,
                $shopNameStartsWith,
                $tokenSession
            );
            $responseData = $response->toArray();
        } catch (Error400Exception|Error500Exception|NetworkException $e) {
            $responseData = $e->getResponse()->toArray(false);
        } catch (DecodingException $e) {
            $responseData = [
                'data' => [],
                'errors' => ['shop_not_found' => 'Shop not found'],
            ];
        } finally {
            return [
                'data' => $responseData['data'],
                'errors' => $responseData['errors'],
            ];
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestShopsGetData(string $groupId, array|null $shopsId, array|null $productsId, string|null $shopName, string|null $shopNameStartsWith, string $tokenSession): HttpClientResponseInterface
    {
        $parameters = [
            'group_id' => $groupId,
            'shops_id' => null !== $shopsId ? implode(',', $shopsId) : null,
            'products_id' => null !== $productsId ? implode(',', $productsId) : null,
            'shop_name' => $shopName,
            'shop_name_starts_with' => $shopNameStartsWith,
        ];

        return $this->httpClient->request(
            'GET',
            self::GET_SHOP_DATA."?{$this->createQueryParameters(array_keys($parameters), array_values($parameters))}",
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
