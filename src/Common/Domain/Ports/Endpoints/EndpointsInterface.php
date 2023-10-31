<?php

declare(strict_types=1);

namespace Common\Domain\Ports\Endpoints;

use Symfony\Component\HttpFoundation\File\UploadedFile;

interface EndpointsInterface
{
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

    public function productCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array;

    public function shopCreate(string $groupId, string $name, string $description, UploadedFile|null $image, string $tokenSession): array;
}
