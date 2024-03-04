<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersListItemComponent',
    template: 'Components/ListOrders/ListOrdersHome/ListItem/ListOrdersListItemComponent.html.twig'
)]
final class ListOrdersListItemComponent extends HomeListItemComponent
{
    public readonly string $productDataJson;

    public static function getComponentName(): string
    {
        return 'ListOrdersListItemComponent';
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
        $this->lang = new ListOrdersListItemComponentLangDto(
            $this->translate('list_orders_modify_button.alt'),
            $this->translate('list_orders_modify_button.title'),
            $this->translate('list_orders_remove_button.alt'),
            $this->translate('list_orders_remove_button.title'),
            $this->translate('list_orders_info_button.alt'),
            $this->translate('list_orders_info_button.title'),
            $this->translate('list_orders_image.alt'),
            $this->translate('list_orders_image.title'),
        );
    }

    private function parseItemDataToJson(ListOrdersListItemComponentDto $listOrdersData): string
    {
        $listOrdersDataToParse = [
            'id' => $listOrdersData->id,
            'name' => $listOrdersData->name,
            'description' => $listOrdersData->description,
            'image' => $listOrdersData->image,
            'createdOn' => $listOrdersData->createdOn->format('Y-m-d'),
        ];

        return json_encode($listOrdersDataToParse, JSON_THROW_ON_ERROR);
    }
}
