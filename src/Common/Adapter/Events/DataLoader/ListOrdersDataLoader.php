<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\ListOrdersDataResponse;
use App\Twig\Components\HomeSection\SearchBar\NAME_FILTERS;
use App\Twig\Components\HomeSection\SearchBar\SECTION_FILTERS;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestListOrdersNameException;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\ParameterBag;

class ListOrdersDataLoader
{
    public function __construct(
        private Endpoints $endpoints
    ) {
    }

    /**
     * @throws JwtTokenGetPayLoadException
     */
    public function load(ParameterBag $attributes, ?string $groupId, ?string $tokenSession): ?ListOrdersDataResponse
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        if (null === $groupId) {
            return null;
        }

        $listOrdersNameDecoded = $attributes->get('list_orders_name');

        if (null === $listOrdersNameDecoded) {
            return null;
        }

        $listOrdersData = $this->endpoints->listOrdersGetData(
            $groupId,
            null,
            true,
            $listOrdersNameDecoded,
            SECTION_FILTERS::LIST_ORDERS->value,
            NAME_FILTERS::EQUALS->value,
            1,
            1,
            $tokenSession
        );

        if (!empty($listOrdersData['errors'])) {
            throw RequestListOrdersNameException::fromMessage('List orders not found');
        }

        return ListOrdersDataResponse::fromArray($listOrdersData['data']['list_orders'][0]);
    }
}
