<?php

declare(strict_types=1);

namespace App\Twig\Components\Product\ProductHome\ListItem;

use App\Controller\Request\Response\ProductShopPriceDataResponse;
use App\Controller\Request\Response\ShopDataResponse;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentLangDto;
use App\Twig\Components\TwigComponentDtoInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ProductListItemComponent',
    template: 'Components/Product/ProductHome/ListItem/ProductListItemComponent.html.twig'
)]
final class ProductListItemComponent extends HomeListItemComponent
{
    public HomeListItemComponentLangDto $lang;
    public ProductListItemComponentDto|TwigComponentDtoInterface $data;

    public readonly string $productDataJson;

    public static function getComponentName(): string
    {
        return 'ProductListItemComponent';
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
        $this->lang = new ProductListItemComponentLangDto(
            $this->translate('product_modify_button.alt'),
            $this->translate('product_modify_button.title'),
            $this->translate('product_remove_button.alt'),
            $this->translate('product_remove_button.title'),
            $this->translate('product_info_button.alt'),
            $this->translate('product_info_button.title'),
            $this->translate('product_image.alt'),
            $this->translate('product_image.title'),
        );
    }

    private function parseItemDataToJson(ProductListItemComponentDto $productData): string
    {
        /** @var ProductShopPriceDataResponse[] $productShopsPricesDataByShopId */
        $productShopsPricesDataByShopId = array_combine(
            array_map(
                fn (ProductShopPriceDataResponse $productShopPrice) => $productShopPrice->shopId,
                $productData->productsShopsPrice
            ),
            $productData->productsShopsPrice
        );

        $productShopsData = array_map(
            fn (ShopDataResponse $shopData) => [
                'id' => $shopData->id,
                'name' => $shopData->name,
                'description' => $shopData->description,
                'image' => $shopData->image,
                'price' => $productShopsPricesDataByShopId[$shopData->id]->price,
                'unit' => $productShopsPricesDataByShopId[$shopData->id]->unitMeasure,
            ],
            $productData->shops
        );

        $productDataToParse = [
            'id' => $productData->id,
            'name' => $productData->name,
            'description' => $productData->description,
            'image' => $productData->image,
            'noImage' => $productData->noImage,
            'createdOn' => $productData->createdOn->format('Y-m-d'),
            'itemsPrices' => $productShopsData,
        ];

        return json_encode($productDataToParse, JSON_THROW_ON_ERROR);
    }
}
