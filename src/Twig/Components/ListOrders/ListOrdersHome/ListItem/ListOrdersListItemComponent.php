<?php

declare(strict_types=1);

namespace App\Twig\Components\ListOrders\ListOrdersHome\ListItem;

use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponent;
use App\Twig\Components\HomeSection\HomeList\ListItem\HomeListItemComponentDto;
use Common\Domain\CodedUrlParameter\UrlEncoder;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(
    name: 'ListOrdersListItemComponent',
    template: 'Components/ListOrders/ListOrdersHome/ListItem/ListOrdersListItemComponent.html.twig'
)]
final class ListOrdersListItemComponent extends HomeListItemComponent
{
    use UrlEncoder;

    private const LIST_ORDERS_NAME_PLACEHOLDER = '--list_orders_name--';

    public readonly string $productDataJson;
    public readonly string $urlListOrders;

    public static function getComponentName(): string
    {
        return 'ListOrdersListItemComponent';
    }

    /**
     * @param ListOrdersListItemComponentDto $data
     */
    public function mount(HomeListItemComponentDto $data): void
    {
        $this->data = $data;
        $this->loadTranslation();
        $this->urlListOrders = $this->parseItemUrlListItemsPlaceholder($data->urlLinkListOrders, $data->name);
        $this->productDataJson = $this->parseItemDataToJson($data);
    }

    protected function loadTranslation(): void
    {
        $this->setTranslationDomainName($this->data->translationDomainName);
        $this->lang = new ListOrdersListItemComponentLangDto(
            $this->translate('list_orders_modify_button.label'),
            $this->translate('list_orders_modify_button.alt'),
            $this->translate('list_orders_modify_button.title'),
            $this->translate('list_orders_remove_button.label'),
            $this->translate('list_orders_remove_button.alt'),
            $this->translate('list_orders_remove_button.title'),
            $this->translate('list_orders_info_button.label'),
            $this->translate('list_orders_info_button.alt'),
            $this->translate('list_orders_info_button.title'),
            $this->translate('list_orders_link_to_orders_button.title'),
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
            'noImage' => true,
            'createdOn' => $listOrdersData->createdOn->format('Y-m-d'),
            'dateToBuy' => $listOrdersData->dateToBuy?->format('Y-m-d H:i:s'),
        ];

        return json_encode($listOrdersDataToParse, JSON_THROW_ON_ERROR);
    }

    private function parseItemUrlListItemsPlaceholder(string $urlListOrdersPlaceholder, string $listOrdersName): string
    {
        $listOrdersNameDecoded = $this->encodeUrl(mb_strtolower($listOrdersName));

        return mb_ereg_replace(self::LIST_ORDERS_NAME_PLACEHOLDER, $listOrdersNameDecoded, $urlListOrdersPlaceholder);
    }
}
