<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\ProductDataResponse;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestProductNameException;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\ParameterBag;

class ProductDataLoader
{
    use CodedUrlParameter;

    public function __construct(
        private Endpoints $endpoints,
    ) {
    }

    public function load(ParameterBag $attributes, ?string $groupId, ?string $tokenSession): ?ProductDataResponse
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        if (null === $groupId) {
            return null;
        }

        $productNameDecoded = $this->decodeUrlParameter($attributes, 'product_name');

        if (null === $productNameDecoded) {
            return null;
        }

        $productData = $this->endpoints->productGetData(
            $groupId,
            null,
            null,
            $productNameDecoded,
            null,
            null,
            null,
            null,
            1,
            1,
            true,
            $tokenSession
        );

        if (!empty($productData['errors'])) {
            throw RequestProductNameException::fromMessage('Product data not found');
        }

        return ProductDataResponse::fromArray($productData['data']['products'][0]);
    }
}
