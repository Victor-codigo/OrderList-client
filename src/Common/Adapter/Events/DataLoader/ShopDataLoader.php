<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\ShopDataResponse;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestShopNameException;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\ParameterBag;

class ShopDataLoader
{
    use CodedUrlParameter;

    public function __construct(
        private Endpoints $endpoints,
    ) {
    }

    public function load(ParameterBag $attributes, ?string $groupId, ?string $tokenSession): ?ShopDataResponse
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        if (null === $groupId) {
            return null;
        }

        $shopId = $this->loadParamShopId($attributes);
        $shopNameDecoded = $this->loadParamShopName($attributes);

        if (null === $shopId && null === $shopNameDecoded) {
            return null;
        }

        $shopData = $this->endpoints->shopsGetData(
            $groupId,
            $shopId,
            null,
            $shopNameDecoded,
            null,
            null,
            1,
            1,
            true,
            $tokenSession
        );

        if (!empty($shopData['errors'])) {
            throw RequestShopNameException::fromMessage('Group data not found');
        }

        return ShopDataResponse::fromArray($shopData['data']['shops'][0]);
    }

    public function loadParamShopId(ParameterBag $attributes): ?array
    {
        if (!$attributes->has('shop_id')) {
            return null;
        }

        return [$attributes->get('shop_id')];
    }

    public function loadParamShopName(ParameterBag $attributes): ?string
    {
        if (!$attributes->has('shop_name')) {
            return null;
        }

        $shopNameDecoded = $this->decodeUrlParameter($attributes, 'shop_name');

        if (null === $shopNameDecoded) {
            return null;
        }

        return $shopNameDecoded;
    }
}
