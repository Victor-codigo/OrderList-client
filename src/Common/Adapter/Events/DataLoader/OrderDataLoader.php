<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\OrderDataResponse;
use App\Twig\Components\HomeSection\SearchBar\NAME_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestListOrdersNameException;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\ParameterBag;

class OrderDataLoader
{
    use CodedUrlParameter;

    public function __construct(
        private Endpoints $endpoints
    ) {
    }

    public function load(ParameterBag $attributes, ?string $groupId, ?string $tokenSession): ?OrderDataResponse
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        if (null === $groupId) {
            return null;
        }

        $orderNameDecoded = $this->decodeUrlParameter($attributes, 'order_name');

        if (null === $orderNameDecoded) {
            return null;
        }

        $orderData = $this->endpoints->ordersGetData(
            $groupId,
            null,
            null,
            1,
            1,
            true,
            SECTION_FILTERS::PRODUCT->value,
            NAME_FILTERS::EQUALS->value,
            $orderNameDecoded,
            $tokenSession
        );

        if (!empty($orderData['errors'])) {
            throw RequestListOrdersNameException::fromMessage('Order not found');
        }

        return OrderDataResponse::fromArray($orderData['data']['orders'][0]);
    }
}
