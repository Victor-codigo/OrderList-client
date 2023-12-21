<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Endpoints;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface EndpointsInterface
{
    public function decodeUrlName(string|null $name): string|null;

    public function encodeUrl(string $url): string;

    public function listOrdersGetOrders(string $groupId, string $listOrdersId, int $page, int $pageItems, string $tokenSession): array;

    public function listOrdersGetData(string $groupId, string $listOrderName, string $tokenSession): array;

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
    public function productCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array;

    /**
     * @throws UnsupportedOptionException
     */
    public function productModify(string $groupId, string $productId, string|null $shopId, string|null $name, string|null $description, float|null $price, UploadedFile|null $image, bool $imageRemove, string $tokenSession): array;

    /**
     * @return array<{
     *    data: array<string, mixed>,
     *    errors: array<string, mixed>
     * }>
     */
    public function productGetData(string $groupId, array|null $productsId, array|null $shopsId, string|null $productName, string|null $productNameFilterType, string|null $productNameFilterValue, string|null $shopNameFilterFilter, string|null $shopNameFilterValue, int $page, int $pageItems, bool $orderAsc, string $tokenSession): array;

    public function shopCreate(string $groupId, string $name, string|null $description, UploadedFile|null $image, string $tokenSession): array;

    public function shopModify(string $shopId, string $groupId, string $name, string|null $description, UploadedFile|null $image, bool $imageRemove, string $tokenSession): array;

    public function shopsGetData(string $groupId, array|null $shopsId, array|null $productsId, string|null $shopName, string|null $shopNameFilterType, string|null $shopNameFilterValue, int $page, int $pageItems, bool $orderAsc, string $tokenSession): array;

    public function shopRemove(string $groupId, array|null $shopsId, string $tokenSession): array;
}
