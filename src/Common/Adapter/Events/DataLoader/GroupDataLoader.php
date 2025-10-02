<?php

declare(strict_types=1);

namespace Common\Adapter\Events\DataLoader;

use App\Controller\Request\Response\GroupDataResponse;
use Common\Adapter\Endpoints\Endpoints;
use Common\Adapter\Events\Exceptions\RequestGroupNameException;
use Common\Adapter\Events\Exceptions\RequestUserException;
use Common\Domain\CodedUrlParameter\CodedUrlParameter;
use Common\Domain\Config\Config;
use Common\Domain\JwtToken\JwtToken;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

class GroupDataLoader
{
    use CodedUrlParameter;

    public function __construct(
        private Endpoints $endpoints,
        private UserDataLoader $userDataLoader,
    ) {
    }

    /**
     * @throws RequestGroupNameException
     * @throws JwtTokenGetPayLoadException
     */
    public function load(Request $request, ?string $tokenSession): ?GroupDataResponse
    {
        if (!$this->groupHasPermissions($request->getPathInfo(), $tokenSession)) {
            throw new RequestUserException('User has not Permissions');
        }

        if (!JwtToken::hasSessionActive($tokenSession)) {
            return null;
        }

        if ($request->attributes->has('group_name')) {
            return $this->groupData($request->attributes, $tokenSession);
        }

        return $this->userGroupsData($tokenSession);
    }

    public function getGroupNameUrlEncoded(?GroupDataResponse $groupData): ?string
    {
        if (null === $groupData) {
            return null;
        }

        return $this->encodeUrlParameter($groupData->name);
    }

    private function groupData(ParameterBag $attributes, string $tokenSession): ?GroupDataResponse
    {
        $groupNameDecoded = $this->decodeUrlParameter($attributes, 'group_name');

        if (null === $groupNameDecoded) {
            return null;
        }

        $groupData = $this->endpoints->groupGetDataByName($groupNameDecoded, $tokenSession);

        if (!empty($groupData['errors'])) {
            throw RequestGroupNameException::fromMessage('Group data not found');
        }

        return GroupDataResponse::fromArray($groupData['data']);
    }

    private function userGroupsData(string $tokenSession): GroupDataResponse
    {
        $groupData = $this->endpoints->userGroupsGetData(
            null,
            null,
            null,
            1,
            1,
            'user',
            true,
            $tokenSession
        );

        if (!empty($groupData['errors'])) {
            throw RequestGroupNameException::fromMessage('Group user data not found');
        }

        return GroupDataResponse::fromArray($groupData['data']['groups'][0]);
    }

    /**
     * @throws JwtTokenGetPayLoadException
     */
    private function groupHasPermissions(string $urlPath, ?string $tokenSession): bool
    {
        $patternUser = '/^\/('.Config::CLIENT_DOMAIN_LOCALE_VALID.')\/user\/(?!profile)/u';
        $patternHome = '/^(\/$|\/('.Config::CLIENT_DOMAIN_LOCALE_VALID.')\/home)/u';
        $patternLegal = '/^\/('.Config::CLIENT_DOMAIN_LOCALE_VALID.')\/legal/u';
        $patternShareListOrders = '/^\/('.Config::CLIENT_DOMAIN_LOCALE_VALID.')\/share/u';

        // No need permissions
        if (1 === preg_match($patternUser, $urlPath)
        || 1 === preg_match($patternHome, $urlPath)
        || 1 === preg_match($patternLegal, $urlPath)
        || 1 === preg_match($patternShareListOrders, $urlPath)) {
            return true;
        }

        // Need permissions
        if (!JwtToken::hasSessionActive($tokenSession)) {
            return false;
        }

        return true;
    }
}
