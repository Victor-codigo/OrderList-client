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
    public function listOrdersModify(string $groupId, string $listOrdersId, string $name, ?string $description, ?\DateTime $dateToBuy, string $tokenSession): array;

    /**
     * @return array<string, array> "data" and "errors" as index
     */
    public function listOrdersDeleteOrders(string $groupId, string $listOrdersId, array $ordersId, string $tokenSession): array;

    public function groupGetDataByName(string $groupName, string $tokenSession): array;

    /**
     * @return array<string, mixed> index: page -> int,
     *                              pages_total -> int,
     *                              orders -> array of orders
     */
    public function ordersGroupGetData(string $groupId, int $page, int $pageItems, string $tokenSession): array;

    public function ordersDelete(string $groupId, array $ordersId, string $tokenSession): array;

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
}
