<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\HttpClient\Exception\Error400Exception;
use Common\Domain\HttpClient\Exception\Error500Exception;
use Common\Domain\HttpClient\Exception\NetworkException;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ProductsEndPoint
{
    private const POST_PRODUCT_CREATE = Endpoints::API_DOMAIN.'/api/v'.Endpoints::API_VERSION.'/products';

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

    public function productCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array
    {
        try {
            $response = $this->requestProductCreate($groupId, $name, $description, $image, $tokenSession);
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
}
