<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Endpoints;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface EndpointsInterface
{
    public function decodeUrlName(?string $name): ?string;

    public function encodeUrl(string $url): string;

    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersGetData(string $groupId, ?array $listOrdersId, bool $orderAsc, ?string $filterValue, ?string $filterSection, ?string $filterText, int $page, int $pageItems, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreate(string $groupId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function listOrdersCreateFrom(string $groupId, string $listOrdersIdCreateFrom, string $name, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function listOrdersModify(string $groupId, string $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array;

    /**
     * @param string[] $listsOrdersId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersRemove(string $groupId, array $listsOrdersId, string $tokenSession): array;

    /**
     * @param string[] $listsOrdersId
     *
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function listOrdersRemoveOrders(string $groupId, array $listsOrdersId, string $tokenSession): array;

    public function groupGetDataByName(string $groupName, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function userGroupsGetData(?string $filterSection, ?string $filterText, ?string $filterValue, int $page, int $pageItems, bool $orderAsc, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     * @throws RequestUnauthorizedException
     */
    public function groupGetUsersData(string $groupId, int $page, int $pageItems, ?string $filterSection, ?string $filterText, ?string $filterValue, bool $orderAsc, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupUsersAdd(string $groupId, array $usersId, bool $admin, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupUserRemove(string $groupId, array $usersId, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupCreate(string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function groupModify(string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array;

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
    public function groupRemove(array $groupsId, string $tokenSession): array;

    /**
     * @return array<string, mixed> index: page -> int,
     *                              pages_total -> int,
     *                              orders -> array of orders
     */
    public function ordersGetData(string $groupId, ?array $ordersId, ?string $listOrdersId, int $page, int $pageItems, bool $orderAsc, ?string $filterSection, ?string $filterText, ?string $filterValue, string $tokenSession): array;

    /**
     * @throws UnsupportedOptionException
     */
    public function listOrdersGetPrice(?array $listOrdersId, string $groupId, string $tokenSession): array;

    public function ordersRemove(string $groupId, array $ordersId, string $tokenSession): array;

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
    public function ordersCreate(string $groupId, string $listOrdersId, array $ordersData, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function orderModify(string $groupId, string $listOrdersId, string $orderId, string $productId, ?string $shopId, ?string $description, float $amount, string $tokenSession): array;

    /**
     * @throws UnsupportedOptionException
     */
    public function productCreate(string $groupId, string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array;

    /**
     * @throws UnsupportedOptionException
     */
    public function productModify(string $groupId, string $productId, ?string $shopId, ?string $name, ?string $description, ?float $price, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array;

    /**
     * @param string[] $shopsId
     * @param string[] $productsId
     *
     * @throws UnsupportedOptionException
     * @throws RequestException
     * @throws RequestUnauthorizedException
     */
    public function productRemove(string $groupId, array $productsId, ?array $shopsId, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function productGetData(string $groupId, ?array $productsId, ?array $shopsId, ?string $productName, ?string $productNameFilterType, ?string $productNameFilterValue, ?string $shopNameFilterFilter, ?string $shopNameFilterValue, int $page, int $pageItems, bool $orderAsc, string $tokenSession): array;

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
    public function getProductShopPrice(string $groupId, array $productsId, array $shopsId, string $tokenSession): array;

    /**
     * @param string[] $productsOrShopsId
     * @param float[]  $prices
     *
     * @throws UnsupportedOptionException
     */
    public function setProductShopPrice(string $groupId, ?string $productId, ?string $shopId, array $productsOrShopsId, array $prices, array $unitsMeasure, string $tokenSession): array;

    public function shopCreate(string $groupId, string $name, ?string $description, ?UploadedFile $image, string $tokenSession): array;

    public function shopModify(string $shopId, string $groupId, string $name, ?string $description, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array;

    public function shopsGetData(string $groupId, ?array $shopsId, ?array $productsId, ?string $shopName, ?string $shopNameFilterType, ?string $shopNameFilterValue, int $page, int $pageItems, bool $orderAsc, string $tokenSession): array;

    public function shopRemove(string $groupId, ?array $shopsId, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<{
     *      token_session: string|null
     *    }>
     *    errors: array
     * }>
     */
    public function userLogin(string $userName, string $password): array;

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    users: array<int, array>
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetData(array $usersId, string $tokenSession): array;

    /**
     * @return array<{
     *    page: int,
     *    pages_total: int,
     *    users: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function usersGetDataByName(array $usersName, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userEmailChange(string $email, string $password, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userPasswordChange(string $userId, string $passwordOld, string $passwordNew, string $passwordNewRepeat, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<{ id: string }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userModify(string $name, ?UploadedFile $image, bool $imageRemove, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<{ id: string }>
     *    errors: array
     * }>
     *
     * @throws UnsupportedOptionException
     */
    public function userRemove(string $userId, string $tokenSession): array;
}
