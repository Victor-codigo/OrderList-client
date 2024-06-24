<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\Events\Exceptions\RequestUnauthorizedException;
use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Common\Domain\Cookie\Cookie;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Common\Domain\Ports\HttpClient\HttpClientResponseInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UsersEndpoint extends EndpointBase
{
    use UrlEncoder;
    use Cookie;

    public const POST_LOGIN_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/login';
    public const GET_USER_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users';
    public const GET_USER_BY_NAME_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/name';
    public const POST_USER_MODIFY_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/modify';
    public const DELETE_USER_REMOVE_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/remove';
    public const PATCH_PROFILE_EMAIL_CHANGE_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/email';
    public const PATCH_PROFILE_PASSWORD_CHANGE_ENDPOINT = HTTP_CLIENT_CONFIGURATION::API_DOMAIN.'/api/v1/users/password';

    public static ?self $instance = null;

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
     *    data: array<{
     *      token_session: string|null
     *    }>
     *    errors: array
     * }>
     */
    public function userLogin(string $userName, string $password): array
    {
        try {
            $response = $this->requestLogin($userName, $password);
            $tokenSession = $this->apiResponseManage($response,
                fn (array $responseDataError) => null,
                fn (array $responseDataOk) => null,
                function (array $responseDataNoContent) use ($response): ?string {
                    $headers = $response->getHeaders();

                    return $this->getCookieValue($headers['set-cookie'][0]);
                });

            return [
                'data' => [
                    'token_session' => $tokenSession,
                ],
                'errors' => [],
            ];
        } catch (RequestUnauthorizedException $e) {
            return [
                'data' => [],
                'errors' => ['error_login'],
            ];
        } catch (\Throwable $e) {
            return [
                'data' => [],
                'errors' => ['internal_server_error'],
            ];
        }
    }

    /**
     * @throws UnsupportedOptionException
     */
    private function requestLogin(string $userName, string $password): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'POST',
            self::POST_LOGIN_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'username' => $userName,
                'password' => $password,
            ])
        );
    }

    /**
     * @return array<{
     *    data: array<{
     *      page: int,
     *      pages_total: int,
     *      users: array<int, array>
     *    }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetData(array $usersId, string $tokenSession): array
    {
        $response = $this->requestUsersData($usersId, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => [],
                ],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => $responseDataOk['data'],
                ],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => [],
                ],
                'errors' => [],
            ]
        );
    }

    private function requestUsersData(array $usersId, string $tokenSession): HttpClientResponseInterface
    {
        $usersId = implode(',', $usersId);

        return $this->httpClient->request(
            'GET',
            self::GET_USER_ENDPOINT."/{$usersId}",
            HTTP_CLIENT_CONFIGURATION::json(null, $tokenSession)
        );
    }

    /**
     * @return array<{
     *    data: array<{
     *      page: int,
     *      pages_total: int,
     *      users: array<int, array>
     *    }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetDataByName(array $usersName, string $tokenSession): array
    {
        $response = $this->requestUsersDataByName($usersName, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => [],
                ],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => $responseDataOk['data'],
                ],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [
                    'page' => 1,
                    'pages_total' => 1,
                    'users' => [],
                ],
                'errors' => [],
            ]
        );
    }

    private function requestUsersDataByName(array $usersName, string $tokenSession): HttpClientResponseInterface
    {
        $usersNameEncoded = array_map(
            fn (string $userName) => $this->encodeUrl($userName),
            $usersName
        );
        $usersNameAttribute = implode(',', $usersNameEncoded);

        return $this->httpClient->request(
            'GET',
            self::GET_USER_BY_NAME_ENDPOINT."/{$usersNameAttribute}?".HTTP_CLIENT_CONFIGURATION::XDEBUG_VAR,
            HTTP_CLIENT_CONFIGURATION::json(null, $tokenSession)
        );
    }

    /**
     * @return array<{
     *    data: array
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userEmailChange(string $email, string $password, string $tokenSession): array
    {
        $response = $this->requestEmailChange($email, $password, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => [],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [],
                'errors' => [],
            ]
        );
    }

    private function requestEmailChange(string $email, string $password, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'PATCH',
            self::PATCH_PROFILE_EMAIL_CHANGE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json(
                [
                    'email' => $email,
                    'password' => $password,
                ],
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userPasswordChange(string $userId, string $passwordOld, string $passwordNew, string $passwordNewRepeat, string $tokenSession): array
    {
        $response = $this->requestPasswordChange($userId, $passwordOld, $passwordNew, $passwordNewRepeat, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => [],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [],
                'errors' => [],
            ]
        );
    }

    private function requestPasswordChange(string $userId, string $passwordOld, string $passwordNew, string $passwordNewRepeat, string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'PATCH',
            self::PATCH_PROFILE_PASSWORD_CHANGE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([
                'id' => $userId,
                'passwordOld' => $passwordOld,
                'passwordNew' => $passwordNew,
                'passwordNewRepeat' => $passwordNewRepeat,
            ],
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array<{ id: string }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userModify(string $name, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        $response = $this->requestUserModify($name, $image, $imageRemove, $tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => $responseDataOk['data'],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [],
                'errors' => [],
            ]
        );
    }

    private function requestUserModify(string $name, ?UploadedFile $image, bool $imageRemove, string $tokenSession): HttpClientResponseInterface
    {
        if (null !== $image) {
            $paramsFile = [
                'image' => $image,
            ];
        }

        return $this->httpClient->request(
            'POST',
            self::POST_USER_MODIFY_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::form([
                'name' => $name,
                'image_remove' => $imageRemove,
                '_method' => 'PUT',
            ],
                $paramsFile ?? [],
                $tokenSession
            )
        );
    }

    /**
     * @return array<{
     *    data: array<{ id: string }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userRemove(string $tokenSession): array
    {
        $response = $this->requestUserRemove($tokenSession);

        return $this->apiResponseManage($response,
            fn (array $responseDataError) => [
                'data' => [],
                'errors' => $responseDataError['errors'],
            ],
            fn (array $responseDataOk) => [
                'data' => $responseDataOk['data'],
                'errors' => [],
            ],
            fn (array $responseDataNoContent) => [
                'data' => [],
                'errors' => [],
            ]
        );
    }

    private function requestUserRemove(string $tokenSession): HttpClientResponseInterface
    {
        return $this->httpClient->request(
            'DELETE',
            self::DELETE_USER_REMOVE_ENDPOINT,
            HTTP_CLIENT_CONFIGURATION::json([], $tokenSession)
        );
    }
}
