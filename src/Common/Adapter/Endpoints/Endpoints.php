<?php

declare(strict_types=1);

namespace Common\Adapter\Endpoints;

use Common\Adapter\HttpClientConfiguration\HTTP_CLIENT_CONFIGURATION;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Common\Domain\Ports\Endpoints\EndpointsInterface;
use Common\Domain\Ports\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Endpoints implements EndpointsInterface
{
    use UrlEncoder;

    public const API_VERSION = 1;
    public const API_DOMAIN = HTTP_CLIENT_CONFIGURATION::API_DOMAIN;

    public function __construct(
        private HttpClientInterface $httpClient
    ) {
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetOrders($groupId, $listOrdersId, $page, $pageItems, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersGetData(
        string $groupId,
        ?array $listOrdersId,
        bool $orderAsc,
        ?string $filterValue,
        ?string $filterSection,
        ?string $filterText,
        int $page,
        int $pageItems,
        string $tokenSession
    ): array {
        $listOrderNameDecoded = $this->decodeUrlName($filterValue);

        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetData(
            $groupId,
            $listOrdersId,
            $orderAsc,
            $listOrderNameDecoded,
            $filterSection,
            $filterText,
            $page,
            $pageItems,
            $tokenSession
        );
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersGetPrice(?array $listOrdersId, string $groupId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersGetPrice($listOrdersId, $groupId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreate(string $groupId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersCreate($groupId, $name, $description, $dateToBuy, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreateFrom(string $groupId, string $listOrdersIdCreateFrom, string $name, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersCreateFrom($groupId, $listOrdersIdCreateFrom, $name, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersModify(string $groupId, string $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersModify($groupId, $listOrdersId, $name, $description, $dateToBuy, $tokenSession);
    }

    /**
     * @return array<string, array> "data" and "errors" as index
     */
    public function listOrdersRemove(string $groupId, array $listsOrdersId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersRemove($groupId, $listsOrdersId, $tokenSession);
    }

    /**
     * @param string[] $listsOrdersId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersRemoveOrders(string $groupId, array $listsOrdersId, string $tokenSession): array
    {
        return ListOrdersEndpoints::getInstance($this->httpClient)->listOrdersRemoveOrders($groupId, $listsOrdersId, $tokenSession);
    }

    public function groupGetDataByName(string $groupName, string $tokenSession): array
    {
        $groupNameDecoded = $this->decodeUrlName($groupName);

        return GroupsEndpoint::getInstance($this->httpClient)->groupGetDataByName($groupNameDecoded, $tokenSession);
    }

    /**
     * @param string[] $groupsId
     *
     * @throws UnsupportedOptionException
     */
    public function groupGetDataById(array $groupsId, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupGetDataById($groupsId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function groupGetUsersData(
        string $groupId,
        int $page,
        int $pageItems,
        ?string $filterSection,
        ?string $filterText,
        ?string $filterValue,
        bool $orderAsc,
        string $tokenSession
    ): array {
        return GroupsEndpoint::getInstance($this->httpClient)->groupGetUsersData(
            $groupId,
            $page,
            $pageItems,
            $filterSection,
            $filterText,
            $filterValue,
            $orderAsc,
            $tokenSession
        );
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function userGroupsGetData(?string $filterSection, ?string $filterText, ?string $filterValue, int $page, int $pageItems, ?string $groupType, bool $orderAsc, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->userGroupsGetData($filterSection, $filterText, $filterValue, $page, $pageItems, $groupType, $orderAsc, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupUsersAdd(string $groupId, array $usersId, bool $admin, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupUsersAdd($groupId, $usersId, $admin, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupUsersChangeRole(string $groupId, array $usersId, bool $admin, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupUsersChangeRole($groupId, $usersId, $admin, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupUserRemove(string $groupId, array $usersId, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupUserRemove($groupId, $usersId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupCreate(string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupCreate($name, $description, $image, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupModify(string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupModify($groupId, $name, $description, $image, $imageRemove, $tokenSession);
    }

    /**
     * @param string[] $groupsId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupRemove(array $groupsId, string $tokenSession): array
    {
        return GroupsEndpoint::getInstance($this->httpClient)->groupRemove($groupsId, $tokenSession);
    }

    /**
     * @return array<string, mixed> index: page -> int,
     *                              pages_total -> int,
     *                              orders -> array of orders
     */
    public function ordersGetData(string $groupId, ?array $ordersId, ?string $listOrdersId, int $page, int $pageItems, bool $orderAsc, ?string $filterSection, ?string $filterText, ?string $filterValue, string $tokenSession): array
    {
        return OrdersEndpoint::getInstance($this->httpClient)->ordersGetData(
            $groupId,
            $ordersId,
            $listOrdersId,
            $page,
            $pageItems,
            $orderAsc,
            $filterSection,
            $filterText,
            $filterValue,
            $tokenSession
        );
    }

    public function ordersRemove(string $groupId, array $ordersId, string $tokenSession): array
    {
        return OrdersEndpoint::getInstance($this->httpClient)->ordersRemove($groupId, $ordersId, $tokenSession);
    }

    /**
     * @param OrderDataDto[] $ordersData
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function ordersCreate(string $groupId, string $listOrdersId, array $ordersData, string $tokenSession): array
    {
        return OrdersEndpoint::getInstance($this->httpClient)->ordersCreate($groupId, $listOrdersId, $ordersData, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function orderModify(string $groupId, string $listOrdersId, string $orderId, string $productId, ?string $shopId, ?string $description, float $amount, string $tokenSession): array
    {
        return OrdersEndpoint::getInstance($this->httpClient)->orderModify($groupId, $listOrdersId, $orderId, $productId, $shopId, $description, $amount, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function productCreate(string $groupId, string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->productCreate($groupId, $name, $description, $image, $tokenSession);
    }

    /**
     * @throws UnsupportedOptionException
     */
    public function productModify(
        string $groupId,
        string $productId,
        ?string $shopId,
        ?string $name,
        ?string $description,
        ?float $price,
        ?UploadedFile $image,
        bool $imageRemove,
        string $tokenSession
    ): array {
        return ProductsEndPoint::getInstance($this->httpClient)->productModify(
            $groupId,
            $productId,
            $shopId,
            $name,
            $description,
            $image,
            $imageRemove,
            $tokenSession
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
    public function productRemove(string $groupId, array $productsId, ?array $shopsId, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->productRemove($groupId, $productsId, $shopsId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function productGetData(
        string $groupId,
        ?array $productsId,
        ?array $shopsId,
        ?string $productName,
        ?string $productNameFilterType,
        ?string $productNameFilterValue,
        ?string $shopNameFilterFilter,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        return ProductsEndPoint::getInstance($this->httpClient)->productGetData(
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
    }

    /**
     * @param string[] $productsOrShopsId
     * @param float[]  $prices
     *
     * @throws UnsupportedOptionException
     */
    public function setProductShopPrice(string $groupId, ?string $productId, ?string $shopId, array $productsOrShopsId, array $prices, array $unitsMeasure, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->setProductShopPrice($groupId, $productId, $shopId, $productsOrShopsId, $prices, $unitsMeasure, $tokenSession);
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
    public function getProductShopPrice(string $groupId, array $productsId, array $shopsId, string $tokenSession): array
    {
        return ProductsEndPoint::getInstance($this->httpClient)->getProductShopPrice($groupId, $productsId, $shopsId, $tokenSession);
    }

    public function shopCreate(string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopCreate($groupId, $name, $address, $description, $image, $tokenSession);
    }

    public function shopModify(string $shopId, string $groupId, string $name, ?string $address, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopModify($shopId, $groupId, $name, $address, $description, $image, $imageRemove, $tokenSession);
    }

    public function shopsGetData(
        string $groupId,
        ?array $shopsId,
        ?array $productsId,
        ?string $shopName,
        ?string $shopNameFilterType,
        ?string $shopNameFilterValue,
        int $page,
        int $pageItems,
        bool $orderAsc,
        string $tokenSession
    ): array {
        return ShopsEndPoint::getInstance($this->httpClient)->shopsGetData(
            $groupId,
            $shopsId,
            $productsId,
            $shopName,
            $shopNameFilterType,
            $shopNameFilterValue,
            $page,
            $pageItems,
            $orderAsc,
            $tokenSession
        );
    }

    public function shopRemove(string $groupId, ?array $shopsId, string $tokenSession): array
    {
        return ShopsEndPoint::getInstance($this->httpClient)->shopRemove($groupId, $shopsId, $tokenSession);
    }

    /**
     * @return array<{
     *    data: array<{
     *      token_session: string|null
     *    }>
     *    errors: array
     * }>
     */
    public function userSignUp(string $name, string $email, string $password, string $emailConfirmationUrl): array
    {
        return UsersEndpoint::getInstance($this->httpClient)->userSignUp($name, $email, $password, $emailConfirmationUrl);
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
        return UsersEndpoint::getInstance($this->httpClient)->userLogin($userName, $password);
    }

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    users: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetData(array $usersId, string $tokenSession): array
    {
        return UsersEndpoint::getInstance($this->httpClient)->usersGetData($usersId, $tokenSession);
    }

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    users: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetDataByName(array $usersName, string $tokenSession): array
    {
        return UsersEndpoint::getInstance($this->httpClient)->usersGetDataByName($usersName, $tokenSession);
    }

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    users: array<int, array>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userEmailChange(string $email, string $password, string $tokenSession): array
    {
        return UsersEndpoint::getInstance($this->httpClient)->userEmailChange($email, $password, $tokenSession);
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
        return UsersEndpoint::getInstance($this->httpClient)->userPasswordChange($userId, $passwordOld, $passwordNew, $passwordNewRepeat, $tokenSession);
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
        return UsersEndpoint::getInstance($this->httpClient)->userModify($name, $image, $imageRemove, $tokenSession);
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
        return UsersEndpoint::getInstance($this->httpClient)->userRemove($tokenSession);
    }

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    notifications: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function notificationGetData(int $page, int $pageItems, string $lang, string $tokenSession): array
    {
        return NotificationsEndPoint::getInstance($this->httpClient)->notificationGetData($page, $pageItems, $lang, $tokenSession);
    }

    /**
     * @param string[] $notificationsId
     *
     * @throws RequestUnauthorizedException
     */
    public function notificationRemove(array $notificationsId, string $tokenSession): array
    {
        return NotificationsEndPoint::getInstance($this->httpClient)->notificationRemove($notificationsId, $tokenSession);
    }

    /**
     * @param string[] $notificationsId
     *
     * @throws UnsupportedOptionException
     */
    public function notificationMarkAsViewed(array $notificationsId, string $tokenSession): array
    {
        return NotificationsEndPoint::getInstance($this->httpClient)->notificationMarkAsViewed($notificationsId, $tokenSession);
    }
}
