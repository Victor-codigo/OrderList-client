<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\UserDataResponse;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestUserException;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\JwtToken\JwtToken;

class UserDataLoader
{
    use CodedUrlParameter;

    public function __construct(
        private Endpoints $endpoints,
    ) {
    }

    /**
     * @throws RequestUserException
     * @throws JwtTokenGetPayLoadException
     */
    public function load(?string $tokenSession): ?UserDataResponse
    {
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        $userId = JwtToken::getUserName($tokenSession);
        $userData = $this->endpoints->usersGetData([$userId], $tokenSession);

        if (!empty($userData['errors'])) {
            throw RequestUserException::fromMessage('User data not found');
        }

        return UserDataResponse::fromArray($userData['data']['users'][0]);
    }
}
