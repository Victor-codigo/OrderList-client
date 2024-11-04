<?php

declare(strict_types=1);

namespace App\Twig\Components\Shop\ShopHome\ListItem;

use App\Controller\Request\Response\ProductDataResponse;
use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ShopListItemComponent',
    template: 'Components/Shop/ShopHome/ListItem/ShopListItemComponent.html.twig'
)]
final class ShopListItemComponent extends HomeListItemComponent
{
    public readonly string $productDataJson;

    public static function getComponentName(): string
    {
        return 'ShopListItemComponent';
    }

    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->productDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new ShopListItemComponentLangDto(
            $this->translate('shop_modify_button.label'),
            $this->translate('shop_modify_button.alt'),
            $this->translate('shop_modify_button.title'),
            $this->translate('shop_remove_button.label'),
            $this->translate('shop_remove_button.alt'),
            $this->translate('shop_remove_button.title'),
            $this->translate('shop_info_button.alt'),
            $this->translate('shop_info_button.title'),
            $this->translate('shop_image.alt'),
            $this->translate('shop_image.title'),
        );
    }

    private function parseItemDataToJson(ShopListItemComponentDto $shopData): string
    {
        /** @var ProductShopPriceDataResponse[] $productShopsPricesDataByProductId */
        $productShopsPricesDataByProductId = array_combine(
            array_map(
                fn (ProductShopPriceDataResponse $productShopPrice) => $productShopPrice->productId,
                $shopData->productsShopsPrice
            ),
            $shopData->productsShopsPrice
        );

        $productShopsData = array_map(
            fn (ProductDataResponse $productData) => [
                'id' => $productData->id,
                'name' => $productData->name,
                'description' => $productData->description,
                'image' => $productData->image,
                'price' => $productShopsPricesDataByProductId[$productData->id]->price,
                'unit' => $productShopsPricesDataByProductId[$productData->id]->unitMeasure,
            ],
            $shopData->products
        );

        $shopDataToParse = [
            'id' => $shopData->id,
            'name' => $shopData->name,
            'address' => $shopData->address,
            'description' => $shopData->description,
            'image' => $shopData->image,
            'noImage' => $shopData->noImage,
            'createdOn' => $shopData->createdOn->format('Y-m-d'),
            'itemsPrices' => $productShopsData,
        ];

        return json_encode($shopDataToParse, JSON_THROW_ON_ERROR);
    }
}
