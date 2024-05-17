<?php

declare(strict_types=1);

namespace Common\Adapter\Router;

use Symfony\Component\Routing\RouterInterface;

class RouterSelector
{
    public function __construct(
        private RouterInterface $router
    ) {
    }

    public function generate(string $name, array $parameters = [], int $referenceType = RouterInterface::ABSOLUTE_PATH): string
    {
        return $this->router->generate($name, $parameters, $referenceType);
    }

    public function generateShopPath(?string $groupType, ?string $groupNameEncoded): string
    {
        return match ($groupType) {
            'group' => $this->router->generate('shop_home_group', [
                'group_type' => $groupType,
                'group_name' => $groupNameEncoded,
                'section' => 'shop',
                'page' => 1,
                'page_items' => 100,
            ]),
            default => $this->router->generate('shop_home_no_group', [
                'section' => 'shop',
                'page' => 1,
                'page_items' => 100,
            ])
        };
    }

    public function getShopRouteName(?string $groupType): string
    {
        return 'group' === $groupType ? 'shop_home_group' : 'shop_home_no_group';
    }

    public function generateProductPath(?string $groupType, ?string $groupNameEncoded): string
    {
        return match ($groupType) {
            'group' => $this->router->generate('product_home_group', [
                'group_type' => $groupType,
                'group_name' => $groupNameEncoded,
                'section' => 'product',
                'page' => 1,
                'page_items' => 100,
            ]),
            default => $this->router->generate('product_home_no_group', [
                'section' => 'product',
                'page' => 1,
                'page_items' => 100,
            ])
        };
    }

    public function getProductRouteName(?string $groupType): string
    {
        return 'group' === $groupType ? 'product_home_group' : 'product_home_no_group';
    }

    public function generateListOrdersPath(?string $groupType, ?string $groupNameEncoded): string
    {
        return match ($groupType) {
            'group' => $this->router->generate('list_orders_home_group', [
                'group_type' => $groupType,
                'group_name' => $groupNameEncoded,
                'section' => 'list-orders',
                'page' => 1,
                'page_items' => 100,
            ]),
            default => $this->router->generate('list_orders_home_no_group', [
                'section' => 'list-orders',
                'page' => 1,
                'page_items' => 100,
            ])
        };
    }

    public function getListOrdersRouteName(?string $groupType): string
    {
        return 'group' === $groupType ? 'list_orders_home_group' : 'list_orders_home_no_group';
    }

    public function generateOrdersPath(?string $groupType, ?string $groupNameEncoded, string $listOrdersNameEncoded): string
    {
        return match ($groupType) {
            'group' => $this->router->generate('order_home_group', [
                'group_type' => $groupType,
                'group_name' => $groupNameEncoded,
                'list_orders_name' => $listOrdersNameEncoded,
                'section' => 'orders',
                'page' => 1,
                'page_items' => 100,
            ]),
            default => $this->router->generate('order_home_no_group', [
                'list_orders_name' => $listOrdersNameEncoded,
                'section' => 'orders',
                'page' => 1,
                'page_items' => 100,
            ])
        };
    }

    public function getOrdersRouteName(?string $groupType): string
    {
        return 'group' === $groupType ? 'order_home_group' : 'order_home_no_group';
    }
}
